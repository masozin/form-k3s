import { reactive } from "vue";

// State dibuat di luar fungsi supaya dipakai bersama (singleton)
const state = reactive({
    open: false,
    type: "info", // success | error | warning | info
    title: "",
    message: "",
    confirmText: "OK",
    cancelText: null,
    resolve: null,
});

export function useAlert() {
    function fire(options = {}) {
        return new Promise((resolve) => {
            Object.assign(state, {
                open: true,
                type: "info",
                title: "",
                message: "",
                confirmText: "OK",
                cancelText: null,
                ...options,
                resolve,
            });
        });
    }

    function close(result = true) {
        state.open = false;
        state.resolve?.(result);
        state.resolve = null;
    }

    return {
        state,
        fire,
        close,
        success: (title, message, opts = {}) =>
            fire({ type: "success", title, message, ...opts }),
        error: (title, message, opts = {}) =>
            fire({ type: "error", title, message, ...opts }),
        warning: (title, message, opts = {}) =>
            fire({ type: "warning", title, message, ...opts }),
        confirm: (title, message, opts = {}) =>
            fire({
                type: "warning",
                title,
                message,
                cancelText: "Batal",
                confirmText: "Ya, lanjutkan",
                ...opts,
            }),
    };
}