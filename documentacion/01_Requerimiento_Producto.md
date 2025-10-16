# Documento de Requerimientos del Producto (PRD) - "Avatar Steward"

* **Producto:** Avatar Steward (Nombre provisional para la versión expandida de Simple Local Avatars)
* **Versión:** 1.0
* **Fecha:** 14 de Octubre de 2025

---

## 1. Resumen Ejecutivo

WordPress depende de Gravatar, un servicio externo, para la gestión de avatares de usuario. Esto presenta problemas de privacidad (filtrado de datos a terceros), rendimiento (llamadas HTTP externas) y falta de control de marca. "Avatar Steward" solucionará esto al ofrecer una suite completa, ligera y auto-alojada para la gestión de avatares, dando el control total al administrador del sitio y a los usuarios.

### Origen y estrategia de transformación

Avatar Steward toma como punto de partida el código GPL del plugin existente [Simple Local Avatars](https://wordpress.org/plugins/simple-local-avatars/). La estrategia consiste en **copiar la base original y transformarla profundamente** para entregar un producto derivado sustancialmente distinto, manteniendo las obligaciones de la licencia GPL:

- Conservar avisos de copyright y redistribuir bajo GPL.
- Documentar claramente la procedencia del código heredado.
- Garantizar que terceros conserven los mismos derechos de uso y modificación.

El trabajo evoluciona la base hacia una **suite integral de gestión de avatares** con mejoras significativas en privacidad, rendimiento, branding y gobernanza (generador de iniciales avanzado, moderación, biblioteca, integraciones sociales, compliance CodeCanyon, etc.).

### Evaluación inicial del proyecto

Un análisis preliminar mediante investigación exploratoria arrojó las siguientes calificaciones (escala 1-10):

- **Demanda: 7/10** — Nicho pero con necesidad aguda en comunidades preocupadas por privacidad y performance.
- **Facilidad de desarrollo: 9/10** — El plugin base es pequeño; un MVP con generador de iniciales puede completarse en un fin de semana.
- **Licenciamiento: 10/10** — GPL puro, sin restricciones para crear una versión derivada.
- **Prospección comercial: 6/10** — Ticket medio más bajo, pero las features Pro justifican compra en sitios de membresía/foros.
- **Viabilidad: 10/10** — Riesgo bajo; excelente proyecto para dominar el ciclo completo de producto premium.

Estas métricas justifican la elección de transformar la base existente para construir una suite premium sostenible.

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
* **RF-P07:** **Auto-borrado de Datos:** El sistema eliminará avatares inactivos (sin uso por 6 meses) avisando al usuario con antelación opcional y registrando la acción.
* **RF-P08:** **Auditoría de Accesos:** El administrador podrá exportar logs de accesos/modificaciones de avatares para cumplir normativas (GDPR, CCPA, LGPD).

## 4. Requerimientos No Funcionales

* **RFN-01 (Rendimiento):** El plugin no debe añadir más de 50ms al tiempo de carga de la página. Todas las operaciones deben ser eficientes.
* **RFN-02 (Seguridad):** Todas las subidas de archivos deben ser sanitizadas y validadas para prevenir vulnerabilidades.
* **RFN-03 (Compatibilidad):** El plugin debe ser compatible con las 3 últimas versiones mayores de WordPress y PHP 7.4+.
* **RFN-04 (Usabilidad):** La interfaz, tanto para el usuario como para el administrador, debe ser intuitiva y estar integrada de forma nativa en el escritorio de WordPress.
* **RFN-05 (Privacidad):** Ofrecer mecanismos nativos de consentimiento, exportación y eliminación de datos personales alineados con GDPR/CCPA.
* **RFN-06 (Formato y Rendimiento):** Convertir automáticamente avatares a WebP (con fallback) y habilitar prefetching inteligente para reducir el TTFB global.

---

## 5. Riesgos

Listado de riesgos identificados, su probabilidad y mitigación propuesta.

- **Riesgo R-01 (Competencia / Copia):** Un competidor podría replicar características Pro rápidamente.
	- Probabilidad: Media. Impacto: Medio.
	- Mitigación: Lanzamiento rápido, documentación clara, roadmap público y atención al soporte y la comunidad.

- **Riesgo R-02 (Compatibilidad con temas/plugins):** Conflictos con temas o plugins populares (ej. filtros de avatar personalizados).
	- Probabilidad: Media. Impacto: Alto.
	- Mitigación: Pruebas automatizadas y manuales con temas y plugins populares, mecanismos de degradado elegante y hooks/filtros bien documentados.

- **Riesgo R-03 (Vulnerabilidades en subidas):** Riesgo de ejecución remota o subida de archivos maliciosos.
	- Probabilidad: Baja. Impacto: Muy Alto.
	- Mitigación: Validación/escaneo de archivos, uso de `wp_check_filetype()`, restricciones MIME, sandboxing, y pruebas de seguridad (SAST/DAST).

- **Riesgo R-04 (Rendimiento en sitios grandes):** Multitud de consultas o redimensionamientos simultáneos que afecten el rendimiento.
	- Probabilidad: Media. Impacto: Alto.
	- Mitigación: Uso de transients, generación diferida (lazy) de thumbnails y almacenamiento en caché, colas para procesamiento de imágenes en masa.

## 6. Restricciones

- **Restricción C-01 (Compatibilidad de versiones):** Soportar PHP >= 7.4 y WordPress >= 5.8. No se garantizará soporte para versiones anteriores.
- **Restricción C-02 (Dependencias de producción):** El plugin debe evitar dependencias externas en tiempo de ejecución (solo devDependencies permitidas).
- **Restricción C-03 (Tamaño del plugin):** El plugin empaquetado no debe exceder 5 MB en la versión gratuita para facilitar la instalación desde el repositorio.
- **Restricción C-04 (Licenciamiento):** La versión Pro seguirá la política de licencias de CodeCanyon (licencia regular de pago único).
- **Restricción C-05 (Idioma):** Todo el producto (interfaces, cadenas de texto) y la documentación interna deberán entregarse exclusivamente en inglés para facilitar la distribución global.

## 7. Suposiciones

- **S-01:** Los sitios de producción tendrán al menos una de las extensiones PHP requeridas (`gd` o `imagick`).
- **S-02:** Los administradores de sitio tienen permisos para configurar contenedores y despliegues en VPS (Ionos) o contenedores en producción.
- **S-03:** La mayoría de usuarios usan navegadores modernos que soportan formatos de imagen comunes (PNG, JPEG, WebP).

## 8. Criterios de Aceptación

La siguiente lista define lo que se considerará “listo” para la entrega del MVP (versión gratuita):

- Usuarios pueden subir un avatar desde el perfil y éste se muestra en todas las ubicaciones donde `get_avatar()` es llamado.
- Las llamadas a Gravatar se reemplazan en una instalación típica sin intervención manual.
- El generador de iniciales produce imágenes legibles y con colores consistentes configurables.
- Las subidas tienen validación básica (tipo MIME, tamaño máximo configurable) y no permiten la ejecución de código.
- Documentación mínima en `README.md` y sección de ayuda en el plugin.

## 9. Dependencias

- **Infraestructura:** Docker para entornos de desarrollo y despliegue; VPS en Ionos con Ubuntu para producción.
- **Tecnológicas:** WordPress >= 5.8, PHP >= 7.4, MySQL/MariaDB, extensiones `gd`/`imagick`.
- **Herramientas de desarrollo:** Composer, NPM, Webpack, PHPUnit para pruebas.

## 10. Métricas y Monitoreo

- **Métricas de Rendimiento:** Tiempo adicional en TTFB/CLS después de activar el plugin (objetivo: < 50ms overhead).
- **Métricas de Uso:** Número de avatares subidos por día, ratio de selección de avatares de la biblioteca vs. subida propia.
- **Errores y Seguridad:** Número de intentos de subida bloqueados por validación, reportes de errores críticos.
- **Conversión (a futuro):** Tasa de conversión de usuarios Free → Pro (objetivo inicial 0.5%).

---

## 11. Mejoras Futuras Planeadas

- Generador enriquecido: variantes gráficas (gradientes, texturas ligeras, formas) y reglas de contraste automáticas para reforzar el branding.
- Experiencia de usuario: onboarding guiado en la administración con plantillas preconfiguradas y contenido educativo in-app.
- Extensiones Pro: filtros inteligentes (IA ligera) que sugieran colores según la identidad de marca y sincronización con CDN privado para servir avatares optimizados.
- Monitorización: panel interno de analítica con métricas de uso, moderación y performance en tiempo real.
- Ecosistema: exposición de hooks y APIs documentadas para que terceros extiendan proveedores de avatares o políticas de moderación.
- Migración asistida: asistente 1-click para importar avatares desde Gravatar o plugins previos y asignar equivalencias.
- Modo bajo ancho de banda: opción para reemplazar imágenes por iniciales o SVG ligeros cuando se detecte conexión limitada.
- Plantillas sectoriales: paquetes de biblioteca y configuraciones preconfiguradas para eLearning, eCommerce y foros comunitarios.
- API de identidad visual: endpoints para compartir paletas de colores y estilos con temas/third-parties.
- Sellos de verificación: insignias configurables para roles clave (moderadores, autores verificados) visibles junto al avatar.

## 12. Análisis del Código Base Original

- El plugin `Simple Local Avatars` centraliza toda la lógica en la clase `Simple_Local_Avatars`, gestionando hooks, metadatos de usuario y rating de contenido en un único archivo (`includes/class-simple-local-avatars.php`).
- El bootstrap (`simple-local-avatars.php`) depende de compatibilidad mínima (`Validator`) y crea instancias globales, lo que limita la extensibilidad y la inyección de dependencias.
- Los puntos de extensión existentes utilizan filtros como `pre_simple_local_avatar_url` y hooks AJAX (`assign_simple_local_avatar_media`), útiles para mapear dónde introducir servicios especializados.
- El soporte multisite condiciona las claves meta con sufijos por sitio (`simple_local_avatar_{blog_id}`), requisito a tener en cuenta durante la refactorización hacia namespaces propios.
- La limpieza en el desinstalador (`simple_local_avatars_uninstall`) elimina metadatos y opciones específicas; debe replicarse en la nueva arquitectura para evitar residuos.

### Plan de Refactorización

- **Modularización**: separar servicios (uploads, generación, moderación, integraciones) en clases dedicadas dentro de `src/`, reemplazando la estructura monolítica.
- **Namespaces**: migrar el prefijo global `Simple_Local_Avatars` a espacios de nombres `AvatarSteward\*` para evitar colisiones con forks y liberar el espacio global.
- **Compatibilidad**: mantener puntos de integración clave (`pre_get_avatar_data`, REST fields, WP-CLI commands) documentando sus equivalentes en la nueva base.
- **Tests y CI**: trasladar y ampliar las pruebas existentes (`tests/`) asegurando cobertura del generador automático y nuevos módulos.
- **Deployment**: adoptar un bootstrap minimalista en `src/` que cargue dependencias vía autoload y permita envíos limpios a CodeCanyon/WordPress.org.

---

## Requisitos de Publicación (CodeCanyon)

El producto debe cumplir los requisitos de calidad y packaging para CodeCanyon. Consulta el checklist adaptado en: `08_CodeCanyon_Checklist.md`.

## Documentos Relacionados

Para una comprensión completa del proyecto, consulta los siguientes documentos:

- [Estrategia de Negocio](02_Estrategia_de_Negocio.md): Detalla el modelo de monetización y mercado objetivo.
- [Estrategia de Marketing](03_Estrategia_de_Marketing.md): Describe las fases de lanzamiento y objetivos de marketing.
- [Plan de Trabajo](04_Plan_de_Trabajo.md): Incluye el cronograma de desarrollo y tareas específicas.
- [Stack Tecnológico](05_Stack_Tecnologico.md): Especifica las tecnologías y entornos utilizados.
- [Guía de Desarrollo](06_Guia_de_Desarrollo.md): Define principios, estándares y arquitectura del código.
- [Metodología de Desarrollo](07_Metodologia_de_Desarrollo.md): Cubre el flujo de trabajo Agile y Git Flow.