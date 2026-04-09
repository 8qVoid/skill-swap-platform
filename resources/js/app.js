import './bootstrap';

const buildAvatarUrl = (base, name) => `${base}${encodeURIComponent(name && name.trim() ? name.trim() : 'Skill Swap')}`;

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('form').forEach((form) => {
        const preview = form.querySelector('[data-profile-preview]');
        const fileInput = form.querySelector('[data-profile-file-input]');
        const nameInput = form.querySelector('[data-profile-name-input]');

        if (!preview || !fileInput) {
            return;
        }

        const fallbackBase = preview.dataset.profilePreviewBase;

        const setFallbackPreview = () => {
            if (!fallbackBase) {
                return;
            }

            preview.src = buildAvatarUrl(fallbackBase, nameInput?.value ?? preview.alt ?? 'Skill Swap');
        };

        fileInput.addEventListener('change', () => {
            const [file] = fileInput.files ?? [];

            if (!file) {
                setFallbackPreview();
                return;
            }

            preview.src = URL.createObjectURL(file);
        });

        nameInput?.addEventListener('input', () => {
            if ((fileInput.files?.length ?? 0) > 0) {
                return;
            }

            setFallbackPreview();
        });
    });
});
