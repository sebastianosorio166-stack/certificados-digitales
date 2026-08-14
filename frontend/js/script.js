const API = '../backend/api';

async function api(path, options = {}) {
  const response = await fetch(`${API}/${path}`, {
    credentials: 'same-origin',
    headers: { 'Content-Type': 'application/json', ...(options.headers || {}) },
    ...options,
  });
  const payload = await response.json().catch(() => ({ mensaje: 'Respuesta inválida del servidor.' }));
  if (!response.ok || !payload.status) throw new Error(payload.mensaje);
  return payload.data;
}

function escapeHtml(value = '') {
  return String(value).replace(/[&<>'"]/g, char => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' })[char]);
}

function showMessage(element, message, type = 'danger') {
  element.textContent = message;
  element.className = `alert alert-${type}`;
  element.hidden = false;
}

function hideMessage(element) {
  element.hidden = true;
}

function formatDate(value) {
  if (!value) return '—';
  return new Intl.DateTimeFormat('es-CO').format(new Date(`${value}T00:00:00`));
}

async function getSession() {
  return api('sesion.php');
}

async function logout() {
  await api('logout.php', { method: 'POST', body: '{}' });
  window.location.href = 'index.html';
}

async function initLogin() {
  const form = document.querySelector('#loginForm');
  const message = document.querySelector('#message');
  form.addEventListener('submit', async event => {
    event.preventDefault();
    hideMessage(message);
    const data = Object.fromEntries(new FormData(form));
    try {
      const user = await api('sesion.php', { method: 'POST', body: JSON.stringify({ accion: 'login', ...data }) });
      window.location.href = user.rol === 'Administrador' ? 'admin.html' : 'perfil.html';
    } catch (error) {
      showMessage(message, error.message);
    }
  });
}

async function initRegister() {
  const form = document.querySelector('#registerForm');
  const message = document.querySelector('#message');
  form.addEventListener('submit', async event => {
    event.preventDefault();
    hideMessage(message);
    try {
      await api('sesion.php', { method: 'POST', body: JSON.stringify({ accion: 'registro', ...Object.fromEntries(new FormData(form)) }) });
      form.reset();
      showMessage(message, 'Registro completado. Ya puedes iniciar sesión.', 'success');
    } catch (error) {
      showMessage(message, error.message);
    }
  });
}

async function initProfile() {
  let user;
  try {
    user = await getSession();
    if (!user) throw new Error();
    if (user.rol === 'Administrador') window.location.href = 'admin.html';
  } catch {
    window.location.href = 'index.html';
    return;
  }

  document.querySelector('#userName').textContent = `${user.nombres} ${user.apellidos}`;
  document.querySelector('#documento').textContent = user.documento;
  for (const field of ['nombres', 'apellidos', 'correo', 'telefono']) document.querySelector(`#${field}`).value = user[field] || '';

  const message = document.querySelector('#message');
  const [solicitudes, certificados] = await Promise.all([api('solicitudes.php'), api('certificados.php')]);
  document.querySelector('#solicitudes').innerHTML = solicitudes.map(item => `<tr><td>${formatDate(item.fecha_solicitud)}</td><td>${escapeHtml(item.estado)}</td><td>${escapeHtml(item.observaciones || '—')}</td></tr>`).join('') || '<tr><td colspan="3">Aún no tienes solicitudes.</td></tr>';
  document.querySelector('#certificados').innerHTML = certificados.map(item => `<tr><td>${escapeHtml(item.codigo)}</td><td>${formatDate(item.fecha_emision)}</td><td>${formatDate(item.fecha_vencimiento)}</td><td>${escapeHtml(item.estado)}</td></tr>`).join('') || '<tr><td colspan="4">No tienes certificados.</td></tr>';

  document.querySelector('#profileForm').addEventListener('submit', async event => {
    event.preventDefault();
    try {
      await api('usuarios.php', { method: 'PUT', body: JSON.stringify(Object.fromEntries(new FormData(event.currentTarget))) });
      showMessage(message, 'Perfil actualizado.', 'success');
    } catch (error) { showMessage(message, error.message); }
  });
  document.querySelector('#requestForm').addEventListener('submit', async event => {
    event.preventDefault();
    try {
      await api('solicitudes.php', { method: 'POST', body: JSON.stringify(Object.fromEntries(new FormData(event.currentTarget))) });
      window.location.reload();
    } catch (error) { showMessage(message, error.message); }
  });
}

async function initAdmin() {
  let user;
  try {
    user = await getSession();
    if (!user || user.rol !== 'Administrador') throw new Error();
  } catch {
    window.location.href = 'index.html';
    return;
  }

  const requests = await api('solicitudes.php');
  const pending = requests.filter(item => item.estado === 'Pendiente');
  document.querySelector('#pendingRequests').innerHTML = pending.map(item => `<tr><td>${escapeHtml(`${item.nombres} ${item.apellidos}`)}</td><td>${escapeHtml(item.documento)}</td><td>${formatDate(item.fecha_solicitud)}</td><td>${escapeHtml(item.observaciones || '—')}</td><td><button class="btn btn-success btn-sm" data-action="aprobar" data-id="${item.id}">Aprobar</button> <button class="btn btn-outline-danger btn-sm" data-action="rechazar" data-id="${item.id}">Rechazar</button></td></tr>`).join('') || '<tr><td colspan="5">No hay solicitudes pendientes.</td></tr>';
  document.querySelector('#requestHistory').innerHTML = requests.map(item => `<tr><td>${escapeHtml(`${item.nombres} ${item.apellidos}`)}</td><td>${escapeHtml(item.estado)}</td><td>${formatDate(item.fecha_solicitud)}</td><td>${escapeHtml(item.observaciones || '—')}</td></tr>`).join('') || '<tr><td colspan="4">No hay solicitudes.</td></tr>';

  document.querySelector('#pendingRequests').addEventListener('click', async event => {
    const button = event.target.closest('button[data-action]');
    if (!button) return;
    const action = button.dataset.action;
    const observaciones = action === 'rechazar' ? window.prompt('Indica el motivo del rechazo:') : 'Solicitud aprobada.';
    if (observaciones === null || observaciones.trim() === '') return;
    const vigencia = action === 'aprobar' ? window.prompt('Vigencia en años (1, 2 o 3):', '1') : undefined;
    try {
      await api('solicitudes.php', { method: 'PATCH', body: JSON.stringify({ id: Number(button.dataset.id), accion: action, observaciones, vigencia: Number(vigencia) }) });
      window.location.reload();
    } catch (error) { window.alert(error.message); }
  });
}

document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[data-logout]').forEach(button => button.addEventListener('click', logout));
  const page = document.body.dataset.page;
  if (page === 'login') initLogin();
  if (page === 'register') initRegister();
  if (page === 'profile') initProfile();
  if (page === 'admin') initAdmin();
});
