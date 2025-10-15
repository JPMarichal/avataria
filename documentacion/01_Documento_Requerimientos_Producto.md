# Documento de Requerimientos del Producto (PRD) - "Avatar Pro"

* **Producto:** Avatar Pro (Nombre sugerido para la versión expandida de Simple Local Avatars)
* **Versión:** 1.0
* **Fecha:** 14 de Octubre de 2025

---

## 1. Resumen Ejecutivo

WordPress depende de Gravatar, un servicio externo, para la gestión de avatares de usuario. Esto presenta problemas de privacidad (filtrado de datos a terceros), rendimiento (llamadas HTTP externas) y falta de control de marca. "Avatar Pro" solucionará esto al ofrecer una suite completa, ligera y auto-alojada para la gestión de avatares, dando el control total al administrador del sitio y a los usuarios.

## 2. Perfiles de Usuario (User Personas)

* **Administrador de Sitio (Ej. Alex):** Quiere un sitio rápido, seguro y con una marca consistente. Le preocupa la privacidad de sus usuarios y desea moderar el contenido subido.
* **Gestor de Comunidad (Ej. María):** Administra un foro o sitio de membresía. Necesita herramientas para que los usuarios personalicen sus perfiles y se sientan parte de la comunidad, de forma segura.
* **Usuario Final (Ej. Carlos):** Es un miembro registrado en el sitio. Quiere subir fácilmente su propia foto de perfil sin tener que registrarse en un servicio de terceros como Gravatar.

## 3. Requerimientos Funcionales

**3.1. Versión Gratuita (En WordPress.org)**

* **RF-G01:** El plugin debe permitir a los usuarios con permisos de edición de perfil subir una imagen desde su dispositivo para usarla como avatar.
* **RF-G02:** El plugin debe reemplazar por completo las llamadas a Gravatar.
* **RF-G03:** El plugin debe integrarse con el campo de avatar nativo del perfil de usuario de WordPress.
* **RF-G04:** **(Nueva Característica Clave)** Generador de Avatares por Iniciales: Si un usuario no sube una imagen, el plugin generará automáticamente un avatar con las iniciales de su nombre de usuario (Ej. "Juan Pérez" -> "JP") sobre un fondo de color personalizable por el administrador.
* **RF-G05:** El administrador podrá establecer un avatar por defecto para todos los usuarios que no hayan subido uno.

**3.2. Versión Pro (En CodeCanyon)**

* **RF-P01:** **Biblioteca de Avatares:** El administrador podrá crear una galería de avatares predefinidos (íconos, ilustraciones) para que los usuarios los seleccionen si no desean subir su propia foto.
* **RF-P02:** **Integración con Redes Sociales:** Los usuarios podrán conectar sus cuentas de Twitter o Facebook (con su consentimiento) para usar su foto de perfil social como avatar en el sitio.
* **RF-P03:** **Herramientas de Moderación:** El administrador tendrá un panel para ver los avatares subidos recientemente y podrá aprobarlos o rechazarlos. Si se rechaza, el usuario volverá a su avatar anterior.
* **RF-P04:** **Soporte para Múltiples Avatares:** Los usuarios podrán subir hasta 5 avatares y elegir cuál de ellos está activo.
* **RF-P05:** **Restricciones de Subida:** El administrador podrá limitar el peso (MB) y las dimensiones (píxeles) de los avatares subidos.
* **RF-P06:** **Soporte para Roles de Usuario:** Permitir o denegar la capacidad de subir avatares según el rol del usuario (Ej. solo "Suscriptores" y "Autores" pueden, "Visitantes" no).

## 4. Requerimientos No Funcionales

* **RFN-01 (Rendimiento):** El plugin no debe añadir más de 50ms al tiempo de carga de la página. Todas las operaciones deben ser eficientes.
* **RFN-02 (Seguridad):** Todas las subidas de archivos deben ser sanitizadas y validadas para prevenir vulnerabilidades.
* **RFN-03 (Compatibilidad):** El plugin debe ser compatible con las 3 últimas versiones mayores de WordPress y PHP 7.4+.
* **RFN-04 (Usabilidad):** La interfaz, tanto para el usuario como para el administrador, debe ser intuitiva y estar integrada de forma nativa en el escritorio de WordPress.