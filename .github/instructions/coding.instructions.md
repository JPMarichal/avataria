# Coding Guidelines for Avatar Steward

1. **Develop only in `src/`**: All new PHP code must live under `src/` using the `AvatarSteward\` namespace. Treat `simple-local-avatars/` as read-only reference material.
2. **Follow architecture docs**: Respect the modular service structure defined in `documentacion/06_Guia_de_Desarrollo.md` (SOLID, SRP), aplicando principios **KISS** y **DRY**. Organiza el código por dominio (Uploads, Generator, Moderation, Integrations, Analytics) y aplica patrones de diseño cuando aporten claridad y extensibilidad.
3. **Use English for all user-facing strings**: All UI text, comments intended for publication, and configuration defaults must be written in English to satisfy `Restricción C-05`.
4. **Maintain WordPress compatibility**: Target WordPress ≥ 5.8 and PHP ≥ 7.4. Use WordPress APIs (`wp_enqueue_script`, `add_action`, `WP_REST_Controller`, etc.) and escape output (`esc_html`, `esc_attr`, `esc_url`).
5. **Avoid global state**: Prefer services instantiated via factories or dependency containers. Expose hooks and filters instead of relying on globals.
6. **Document GPL origin**: When copying/adapting legacy code, add notes to `docs/legal/origen-gpl.md` and keep original license headers intact.
7. **Prepare documentation updates**: Any feature change must update the relevant markdown docs (PRD, plan de trabajo, marketing) and `README.md` in English.
8. **Coding style**: Adhere to WordPress Coding Standards (`phpcs --standard=WordPress`). For JS/CSS, follow the configurations referenced in `documentacion/05_Stack_Tecnologico.md`.
