# Guía de Desarrollo - "Avatar Steward"

* **Proyecto:** Avatar Steward (Provisional)
* **Versión:** 1.0
* **Fecha:** 14 de Octubre de 2025

---

## 1. Filosofía y Principios Fundamentales 🧠

* **KISS (Keep It Simple, Stupid):** Cada función debe ser tan simple como sea posible. Evitar la sobreingeniería. Si una característica se vuelve demasiado compleja, es una señal para reevaluar su enfoque.
* **DRY (Don't Repeat Yourself):** Reutilizar el código a través de funciones, métodos o clases de ayuda. Evitar el código copiado y pegado a toda costa.
* **Performance Primero:** El plugin debe ser imperceptible en el rendimiento del sitio. Todas las consultas a la base de datos deben ser eficientes. No se deben ejecutar operaciones pesadas en cada carga de página. Usar transitorios de WordPress (`transients`) para cachear resultados cuando sea apropiado.

## 2. Estándares de Codificación ✒️

* **PHP:** Se seguirán estrictamente los **Estándares de Codificación de WordPress** (`WordPress-Core`). Esto incluye el uso de `WordPress-Docs` para la documentación de bloques de código. Se validará con `PHP_CodeSniffer`.
* **JavaScript:** Se seguirán los **Estándares de JavaScript de WordPress**, validados con `ESLint`.
* **CSS:** Se seguirá una metodología similar a **BEM (Block, Element, Modifier)** para nombrar las clases de CSS, asegurando que todos los selectores tengan un prefijo único (ej. `.avapro-`) para evitar colisiones con otros plugins o temas.

## 3. Arquitectura de Software y Patrones de Diseño 🏛️

Se adoptará una arquitectura orientada a objetos clara y desacoplada, aplicando los principios **SOLID**.

- **Estructura de carpetas**: El código activo residirá en `src/` siguiendo PSR-4 y namespaces `AvatarSteward\*`. El directorio `simple-local-avatars/` queda como referencia histórica/analítica y no debe mezclarse con el despliegue.
- **Servicios dedicados**: Cada dominio (uploads, generador automático, moderación, integraciones sociales, analítica) se implementará como servicio independiente, reemplazando la clase monolítica `Simple_Local_Avatars` del código original.
- **Wrappers de compatibilidad**: Durante la transición se expondrán adaptadores que conserven hooks clave (`pre_get_avatar_data`, comandos WP-CLI, REST fields) mientras se migran consumidores internos al nuevo núcleo.

* **S - Principio de Responsabilidad Única (Single Responsibility Principle):**
    * Cada clase tendrá una única responsabilidad. Por ejemplo, una clase `Avatar_Uploader` se encargará solo de procesar la subida de archivos, mientras que una clase `Admin_Settings` gestionará la página de configuración.

* **O - Principio de Abierto/Cerrado (Open/Closed Principle):**
    * La funcionalidad del plugin será extensible sin modificar el código existente. Por ejemplo, para añadir nuevos proveedores de avatares sociales (además de Twitter/Facebook), se implementará un **Patrón de Estrategia (Strategy Pattern)**. Existirá una interfaz `Social_Provider_Interface` y cada proveedor (ej. `Twitter_Provider`, `LinkedIn_Provider`) será una implementación concreta de esa interfaz.

* **L - Principio de Sustitución de Liskov (Liskov Substitution Principle):**
    * Cualquier implementación de una de nuestras interfaces (como la mencionada `Social_Provider_Interface`) podrá ser sustituida por otra sin alterar el comportamiento del programa.

* **I - Principio de Segregación de la Interfaz (Interface Segregation Principle):**
    * Se crearán interfaces pequeñas y específicas en lugar de interfaces grandes y monolíticas.

* **D - Principio de Inversión de Dependencias (Dependency Inversion Principle):**
    * Las clases de alto nivel no dependerán de las de bajo nivel, sino de abstracciones. Se utilizará un contenedor de **Inyección de Dependencias (Dependency Injection)** simple o un localizador de servicios para gestionar las instancias de las clases y sus dependencias. Esto es crucial para la modularidad y las pruebas.

### **Patrones de Diseño Adicionales:**

* **Singleton:** Se usará con moderación, principalmente para la clase principal del plugin que orquesta todo y para evitar la instanciación múltiple de los manejadores de hooks.
* **Factory Method:** Se podría usar para crear objetos de diferentes tipos (ej. diferentes tipos de avatares generados) a través de una interfaz común.
* **Observer:** El sistema de ganchos de WordPress (`actions` y `filters`) es una implementación de este patrón. Nos adheriremos a él, haciendo que nuestro plugin sea extensible mediante hooks personalizados.

## 4. Gestión de Hooks de WordPress

* Todos los ganchos (`add_action`, `add_filter`) se registrarán en una clase o método de inicialización centralizado, no de forma dispersa por el código.
* Las funciones de callback serán métodos públicos de clases bien definidas, no funciones globales, para evitar la contaminación del espacio de nombres global.

## 5. Cumplimiento Continuo con CodeCanyon ✅

* Desde el MVP, toda funcionalidad debe incluir actualización de `README.md`, `CHANGELOG.md` y documentación de usuario correspondiente en `docs/`.
* Cada PR debe adjuntar resultados de linting (`phpcs`, ESLint) y pruebas automatizadas (PHPUnit) como evidencia en `docs/reports/`.
* Los assets (capturas, video demo) y licencias asociadas se mantienen versionados y auditables; cualquier recurso nuevo debe registrar su licencia en `docs/licensing.md`.
* Antes de fusionar a `develop`, se revisa la checklist relevante de `08_CodeCanyon_Checklist.md` y se documentan los gaps o acciones tomadas.

## Documentos Relacionados

Para una comprensión completa del proyecto, consulta los siguientes documentos:

- [Documento de Requerimientos del Producto](01_Documento_Requerimientos_Producto.md): Define los requerimientos funcionales.
- [Estrategia de Negocio](02_Estrategia_de_Negocio.md): Detalla el modelo de negocio.
- [Estrategia de Marketing](03_Estrategia_de_Marketing.md): Describe la estrategia de marketing.
- [Plan de Trabajo](04_Plan_de_Trabajo.md): Incluye el cronograma.
- [Stack Tecnológico](05_Stack_Tecnologico.md): Especifica las tecnologías utilizadas.
- [Metodología de Desarrollo](07_Metodologia_de_Desarrollo.md): Cubre el flujo de trabajo y pruebas.
- [CodeCanyon Checklist](08_CodeCanyon_Checklist.md): Requisitos de calidad y packaging para la publicación en CodeCanyon.