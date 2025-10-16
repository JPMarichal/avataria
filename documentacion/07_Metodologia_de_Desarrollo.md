# Metodología de Desarrollo - "Avatar Pro"

* **Proyecto:** Avatar Pro
* **Versión:** 1.0
* **Fecha:** 14 de Octubre de 2025

---

## 1. Metodología General 🚀

Se adoptará una metodología **Agile**, inspirada en los marcos de **Scrum** y **Kanban**, adaptada para un desarrollador o un equipo pequeño. El objetivo es ser flexible, iterativo y centrado en entregar valor de forma continua.

* **Iteraciones (Sprints):** El trabajo se organizará en Sprints de **1 semana**. Cada Sprint tendrá un objetivo claro (ej. "Implementar el generador de avatares por iniciales").
* **Tablero Kanban:** Se utilizará un tablero visual (con herramientas como Trello, Asana, o los Proyectos de GitHub) para gestionar el flujo de trabajo. Las columnas serán:
    1.  **Backlog:** Todas las tareas e ideas definidas en el PRD.
    2.  **To Do (Sprint Actual):** Tareas comprometidas para el Sprint actual.
    3.  **In Progress:** La tarea que se está desarrollando activamente (se fomenta no tener más de 1-2 tareas aquí para mantener el foco).
    4.  **In Review / Testing:** Tareas completadas que esperan una revisión de código o pruebas de calidad.
    5.  **Done:** Tareas completadas y fusionadas en la rama principal.

## 2. Flujo de Trabajo de Git (Git Flow) 🌿

Se utilizará un flujo de trabajo simplificado pero robusto para garantizar la estabilidad del código.

* **`main`:** Esta rama es el código de producción. Siempre debe estar estable y lista para ser desplegada. Solo se fusiona desde la rama `develop`.
* **`develop`:** Esta es la rama principal de desarrollo. Contiene las últimas funcionalidades completadas y probadas.
* **Ramas de Característica (`feature/`):** Cada nueva característica o tarea se desarrollará en su propia rama, partiendo de `develop`.
    * Ejemplo: `feature/avatar-generator`, `feature/moderation-panel`.
* **Pull Requests (PR):** Una vez que una rama de característica está completa, se abre un Pull Request contra la rama `develop`. El PR debe incluir una descripción clara de los cambios.
* **Revisión de Código:** Todo PR debe ser revisado antes de ser fusionado. Esto es crucial para mantener la calidad del código, incluso si lo revisa el mismo desarrollador después de un tiempo.
* **Ramas de Corrección (`hotfix/`):** Si se encuentra un error crítico en producción (`main`), se crea una rama `hotfix/` a partir de `main`, se corrige el error, y se fusiona tanto en `main` como en `develop`.

## 3. Pruebas y Calidad (QA) 

* **Pruebas Unitarias:** Se fomentará la escritura de pruebas unitarias con **PHPUnit** para las clases que contienen lógica de negocio crítica y pura (ej. la clase que genera los avatares a partir de iniciales). Esto es más fácil gracias a la Inversión de Dependencias.
* **Pruebas Manuales:** Antes de finalizar un Sprint, se debe ejecutar una batería de pruebas manuales en un entorno limpio de WordPress, verificando:
    * Compatibilidad con el tema por defecto (Twenty Twenty-Three/Four).
    * Compatibilidad con plugins populares (WooCommerce, Yoast SEO).
    * Funcionamiento en diferentes versiones de PHP.
    * Ausencia de errores en la consola del navegador y en el `debug.log` de WordPress.

## 4. Cumplimiento con CodeCanyon en Agile 

* Cada Sprint incluye la revisión de la checklist correspondiente en `08_CodeCanyon_Checklist.md` y la actualización de un registro en `docs/reports/codecanyon-compliance.md`.
* Definition of Done extendida: `README.md`, `CHANGELOG.md`, documentación en `docs/`, scripts de linting/tests y evidencia de ejecución deben estar actualizados.
* Los recursos nuevos (assets, librerías) se validan por licencias y se agrega la información en `docs/licensing.md` antes de cerrar el Sprint.
* Se planifican demos internas con `docker-compose.demo.yml` al final de cada Sprint para garantizar la reproducibilidad exigida por Envato.

## 5. Documentación 

* **Documentación en Código:** Todo el código (clases, métodos, funciones) debe estar documentado siguiendo los estándares de **WordPress-Docs**.
* **Documentación de Usuario:** Se mantendrá un archivo `README.md` detallado y una documentación de usuario para la versión Pro, explicando cómo configurar y utilizar cada característica.

---

## Documentos Relacionados

Para una comprensión completa del proyecto, consulta los siguientes documentos:

- [Documento de Requerimientos del Producto](01_Documento_Requerimientos_Producto.md): Define los requerimientos del producto.
- [Estrategia de Negocio](02_Estrategia_de_Negocio.md): Detalla el modelo de monetización.
- [Estrategia de Marketing](03_Estrategia_de_Marketing.md): Describe las fases de marketing.
- [Plan de Trabajo y Entregables](04_Plan_de_Trabajo.md): Incluye cronograma, entregables y criterios de aceptación por fase.
- [Stack Tecnológico](05_Stack_Tecnologico.md): Especifica las tecnologías y entornos.
- [Guía de Desarrollo](06_Guia_de_Desarrollo.md): Define principios y estándares de codificación.
- [CodeCanyon Checklist](08_CodeCanyon_Checklist.md): Requisitos de calidad y packaging para la publicación en CodeCanyon.

## Checklist Rápido de Entregables

- Consulta `04_Plan_de_Trabajo.md` para ver los entregables exactos y los criterios de aceptación por fase.
- Antes de abrir un PR, asegúrate de que: tests relevantes pasan, documentación actualizada, y `docker-compose.dev.yml` permite reproducir el smoke test local.