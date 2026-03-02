const BASE_URL = 'http://localhost:8000/api/'

export async function apiFetch(url, options = {}) {
    const token = localStorage.getItem('token')
    
    return fetch(BASE_URL + url, {
        ...options,
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        ...options.headers
    })
}