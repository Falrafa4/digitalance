/**
 * Global API Fetch Wrapper
 */

export async function apiFetch(url, options = {}) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    
    // Use relative path to support both localhost and production
    const baseUrl = '/api/';
    
    const defaultHeaders = {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
    };

    // If there's a bearer token in localStorage (if used by Sanctum/Passport)
    const token = localStorage.getItem('token');
    if (token) {
        defaultHeaders['Authorization'] = `Bearer ${token}`;
    }

    return fetch(baseUrl + url.replace(/^\//, ''), {
        ...options,
        headers: {
            ...defaultHeaders,
            ...options.headers
        }
    });
}