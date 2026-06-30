import { ref } from 'vue';

export interface Toast {
    id: number;
    type: 'success' | 'error' | 'warning' | 'info';
    message: string;
    duration: number;
    visible: boolean;
}

const toasts = ref<Toast[]>([]);
let nextId = 0;

export function useToast() {
    function show(type: Toast['type'], message: string, duration = 3000) {
        const id = ++nextId;
        const toast: Toast = { id, type, message, duration, visible: true };
        toasts.value.push(toast);

        setTimeout(() => {
            const t = toasts.value.find((t) => t.id === id);
            if (t) t.visible = false;
            setTimeout(() => {
                toasts.value = toasts.value.filter((t) => t.id !== id);
            }, 400);
        }, duration);
    }

    function dismiss(id: number) {
        const t = toasts.value.find((t) => t.id === id);
        if (t) t.visible = false;
        setTimeout(() => {
            toasts.value = toasts.value.filter((t) => t.id !== id);
        }, 400);
    }

    return { toasts, show, dismiss };
}
