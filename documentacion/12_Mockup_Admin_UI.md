# Mockup de Interfaz de Administración - Avatar Steward

## 1. Visión General
- **Pantalla principal:** página única en el admin de WordPress bajo el menú "Avatar Steward".
- **Secciones principales:**
  - **Pestaña "General Settings"** (opciones básicas).
  - **Pestaña "Moderation"** (panel de moderación).
- **Diseño:** estructura en dos columnas en cada pestaña, con sidebar contextual en la derecha para ayudas y acciones rápidas.

## 2. Pestaña "General Settings"

### 2.1 Encabezado
- Título: "Avatar Steward · General Settings".
- Descripción breve sobre la finalidad de la pestaña.
- Botón primario "Save Changes" y secundario "Reset to defaults" en la parte superior.

### 2.2 Layout
- **Columna izquierda (70%)**: bloques de ajustes siguiendo estilo WordPress, cada bloque con título, descripción en inglés y campos alineados.
- **Columna derecha (30%)**: tarjetas con información contextual (tips de privacidad, enlaces a documentación en inglés).

### 2.3 Bloques de Ajustes
- **Default Avatar**
  - Selector de imagen (media uploader) con previsualización.
  - Botón "Use default WordPress avatar".
- **Initials Generator**
  - Color picker con 6 presets y opción personalizada.
  - Dropdown para estilo tipográfico (serif, sans, bold).
  - Toggle "Enable low bandwidth mode".
- **Upload Restrictions**
  - Campo numérico "Max file size (MB)".
  - Campos para ancho y alto máximo en píxeles.
  - Checkbox "Convert uploads to WebP".
- **Roles & Permissions**
  - Lista de checkboxes por rol (Subscriber, Author, Editor, etc.).
  - Toggle "Require approval before publish".

### 2.4 Barra lateral
- **Card 1:** "Compliance quick check" con checklist resumida (licencias, GDPR).
- **Card 2:** "Need help?" con enlace a documentación y soporte.

### 2.5 Footer
- Repetir botones "Save" / "Reset".
- Nota sobre idioma: "All public-facing strings must remain in English".

## 3. Pestaña "Moderation"

### 3.1 Encabezado
- Título: "Avatar Steward · Moderation".
- Subtítulo: estado de la cola ("5 avatars pending review").
- Filtros rápidos: Dropdown de estados (Pending, Approved, Rejected), filtrado por rol, búsqueda por usuario.

### 3.2 Layout
- **Columna completa con tabla interactiva**.
- Cada fila representa un avatar pendiente o histórico.

### 3.3 Tabla de Moderación
- Columnas:
  - Avatar (thumbnail 64x64).
  - Usuario (nombre, rol).
  - Fecha de subida.
  - Origen (Upload, Initials, Library, Social).
  - Estado (badge coloreado).
  - Acciones (botones "Approve", "Reject", "View details").
- Checkbox para selección múltiple y acciones masivas (approve/reject/delete).

### 3.4 Panel lateral (slide-over)
- Se activa al pulsar "View details".
- Contiene:
  - Previsualización grande (240x240).
  - Metadatos (tipo de archivo, tamaño, IP de subida, nota del usuario).
  - Historial de decisiones.
  - Campo de comentarios del moderador.

### 3.5 Barra inferior persistente
- Acciones masivas seleccionadas.
- Indicador del número de elementos seleccionados.

## 4. Interacciones Clave
- Guardado asincrónico con notificación (toast) "Settings saved".
- Filtros en moderación aplicados sin recargar página (AJAX).
- Banderas visuales cuando un usuario reincide con rechazos.

## 5. Próximos Entregables Relacionados
- Crear mockups visuales en Figma basados en este blueprint.
- Documentar hooks y endpoints necesarios para que la UI funcione (relación con Tarea 1.3).
- Validar textos en inglés con el equipo de marketing antes de implementarlos.
