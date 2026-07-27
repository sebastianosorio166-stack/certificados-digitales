// ===============================
// LOGIN DEL SISTEMA
// ===============================

function login() {

let usuario = document.getElementById("usuario").value.trim();
let password = document.getElementById("password").value.trim();

// ADMIN PRINCIPAL

if (usuario === "Admin" && password === "1") {

localStorage.setItem("usuarioActivo", "Admin");
localStorage.setItem("rolActivo", "admin");

window.location.href = "dashboard.html";
return;

}

// VALIDAR USUARIOS REGISTRADOS

let usuarios = JSON.parse(localStorage.getItem("usuarios")) || [];

let encontrado = usuarios.find(u =>
u.cedula === usuario && u.password === password
);

if (encontrado) {

localStorage.setItem("usuarioActivo", encontrado.cedula);
localStorage.setItem("rolActivo", encontrado.rol);

window.location.href = "perfil.html";

} else {

document.getElementById("mensaje").innerText =
"Usuario o contraseña incorrectos";

}

}

// ===============================
// REGISTRO USUARIO
// ===============================

function registro() {

let nombres = document.getElementById("nombres").value;
let apellidos = document.getElementById("apellidos").value;
let cedula = document.getElementById("cedula").value;
let celular = document.getElementById("celular").value;
let correo = document.getElementById("correo").value;
let password = document.getElementById("passwordRegistro").value;

let usuarios = JSON.parse(localStorage.getItem("usuarios")) || [];

// VALIDAR EXISTENCIA

let existe = usuarios.find(u => u.cedula === cedula);

if (existe) {

document.getElementById("mensajeRegistro").innerText =
"El usuario ya existe";

return;

}

// CREAR USUARIO

let nuevoUsuario = {

nombres,
apellidos,
cedula,
celular,
correo,
password,

rol: "usuario",

estadoCertificado: "Sin solicitud",

fechaEmision: "-",
fechaCaducidad: "-",
vigencia: "-",

historialCertificados: [],

historialValidaciones: [],

solicitudPendiente: false

};

usuarios.push(nuevoUsuario);

localStorage.setItem("usuarios", JSON.stringify(usuarios));

document.getElementById("mensajeRegistro").innerText =
"Usuario registrado correctamente";

}

// ===============================
// CARGAR PERFIL
// ===============================

function cargarPerfil() {

let cedula = localStorage.getItem("usuarioActivo");

let usuarios = JSON.parse(localStorage.getItem("usuarios")) || [];

let usuario = usuarios.find(u => u.cedula === cedula);

if (!usuario) return;

// DATOS PERSONALES

document.getElementById("nombreUsuario").innerText =
usuario.nombres + " " + usuario.apellidos;

document.getElementById("cedulaUsuario").innerText =
usuario.cedula;

document.getElementById("estadoCertificado").innerText =
usuario.estadoCertificado;

document.getElementById("fechaEmision").innerText =
usuario.fechaEmision;

document.getElementById("fechaCaducidad").innerText =
usuario.fechaCaducidad;

document.getElementById("vigencia").innerText =
usuario.vigencia;

// HISTORIAL

let tabla = document.getElementById("historialCertificados");

tabla.innerHTML = "";

usuario.historialCertificados.forEach(cert => {

tabla.innerHTML += `

<tr>
<td>${cert.nombre}</td>
<td>${cert.cedula}</td>
<td>${cert.fechaEmision}</td>
<td>${cert.fechaCaducidad}</td>
</tr>

`;

});

}

// ===============================
// SOLICITAR CERTIFICADO
// ===============================

function solicitarCertificado() {

let cedula = localStorage.getItem("usuarioActivo");

let usuarios = JSON.parse(localStorage.getItem("usuarios")) || [];

let index = usuarios.findIndex(u => u.cedula === cedula);

if (index === -1) return;

usuarios[index].estadoCertificado = "Pendiente";
usuarios[index].solicitudPendiente = true;

localStorage.setItem("usuarios", JSON.stringify(usuarios));

alert("Solicitud enviada correctamente");

location.reload();

}

// ===============================
// PANEL ADMINISTRADOR
// ===============================

function cargarAdmin() {

let usuarios = JSON.parse(localStorage.getItem("usuarios")) || [];

let pendientes = document.getElementById("tablaPendientes");

let aprobados = document.getElementById("tablaAprobados");

pendientes.innerHTML = "";
aprobados.innerHTML = "";

// RECORRER USUARIOS

usuarios.forEach((u, index) => {

// PENDIENTES

if (u.estadoCertificado === "Pendiente") {

pendientes.innerHTML += `

<tr>

<td>${u.nombres}</td>
<td>${u.apellidos}</td>
<td>${u.cedula}</td>

<td>

<select id="rol${index}">
<option value="usuario">Usuario</option>
<option value="admin">Administrador</option>
</select>

</td>

<td>

<select id="vigencia${index}">
<option value="1 año">1 año</option>
<option value="2 años">2 años</option>
<option value="3 años">3 años</option>
</select>

</td>

<td>

<button onclick="aprobarUsuario(${index})" class="btn btn-success btn-sm">
Aprobar
</button>

<button onclick="rechazarUsuario(${index})" class="btn btn-danger btn-sm">
Rechazar
</button>

</td>

</tr>

`;

}

// APROBADOS

if (u.estadoCertificado === "Aprobado") {

aprobados.innerHTML += `

<tr>

<td>${u.nombres}</td>
<td>${u.apellidos}</td>
<td>${u.cedula}</td>
<td>${u.rol}</td>
<td>${u.vigencia}</td>
<td>${u.fechaCaducidad}</td>

</tr>

`;

}

});

}

// ===============================
// APROBAR USUARIO
// ===============================

function aprobarUsuario(index) {

let usuarios = JSON.parse(localStorage.getItem("usuarios")) || [];

let rol =
document.getElementById(`rol${index}`).value;

let vigencia =
document.getElementById(`vigencia${index}`).value;

// FECHA ACTUAL

let fechaActual = new Date();

let fechaEmision =
fechaActual.toLocaleDateString();

// FECHA CADUCIDAD

let años = parseInt(vigencia);

fechaActual.setFullYear(fechaActual.getFullYear() + años);

let fechaCaducidad =
fechaActual.toLocaleDateString();

// ACTUALIZAR DATOS

usuarios[index].rol = rol;

usuarios[index].estadoCertificado = "Aprobado";

usuarios[index].vigencia = vigencia;

usuarios[index].fechaEmision = fechaEmision;

usuarios[index].fechaCaducidad = fechaCaducidad;

usuarios[index].solicitudPendiente = false;

// HISTORIAL CERTIFICADOS

usuarios[index].historialCertificados.push({

nombre:
usuarios[index].nombres + " " +
usuarios[index].apellidos,

cedula:
usuarios[index].cedula,

fechaEmision,
fechaCaducidad

});

// HISTORIAL VALIDACIONES

usuarios[index].historialValidaciones.push({

admin: "Admin",

accion: "Aprobado",

fecha:
new Date().toLocaleString(),

observacion:
"Certificado aprobado correctamente"

});

localStorage.setItem("usuarios", JSON.stringify(usuarios));

alert("Solicitud aprobada");

location.reload();

}

// ===============================
// RECHAZAR USUARIO
// ===============================

function rechazarUsuario(index) {

let motivo =
prompt("Ingrese motivo del rechazo");

if (!motivo) return;

let usuarios = JSON.parse(localStorage.getItem("usuarios")) || [];

usuarios[index].estadoCertificado = "Rechazado";

usuarios[index].solicitudPendiente = false;

// HISTORIAL VALIDACIONES

usuarios[index].historialValidaciones.push({

admin: "Admin",

accion: "Rechazado",

fecha:
new Date().toLocaleString(),

observacion: motivo

});

localStorage.setItem("usuarios", JSON.stringify(usuarios));

alert("Solicitud rechazada");

location.reload();

}