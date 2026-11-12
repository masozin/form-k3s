// resources/js/utils/safeRoute.js
export function routeExists(name) {
    try {
        route(name)
        return true
    } catch (e) {
        return false
    }
}

export function safeRoute(name, params) {
    return routeExists(name) ? route(name, params) : '#'
}
