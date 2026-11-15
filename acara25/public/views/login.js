const API_BASE_URL = new URL('../', window.location.href);

document.querySelector('#form-login').addEventListener('submit', async (event) => {
    event.preventDefault();

    const form = event.currentTarget;
    const errorMessage = document.querySelector('#login-error');
    const submitButton = form.querySelector('button[type="submit"]');
    errorMessage.textContent = '';
    submitButton.disabled = true;

    try {
        const response = await fetch(new URL('login', API_BASE_URL), {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                username: form.elements.username.value.trim(),
                password: form.elements.password.value
            })
        });
        const result = await response.json();

        if (!response.ok || !result.success || !result.data?.token) {
            throw new Error(result.message || `Login gagal (HTTP ${response.status})`);
        }

        localStorage.setItem('token', result.data.token);
        localStorage.setItem('role', result.data.role);
        localStorage.setItem('username', form.elements.username.value.trim());
        window.location.replace('dashboard.html');
    } catch (error) {
        errorMessage.textContent = error.message || 'Tidak dapat terhubung ke server.';
    } finally {
        submitButton.disabled = false;
    }
});
