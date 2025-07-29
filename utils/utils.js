export async function redirectIfNotLoggedIn() {
    try {
        const response = await fetch("/api/users/me.php", {
            credentials: "include",
        });

        if (!response.ok) {
            throw new Error("Not authenticated");
        }

        const user = await response.json();

        if (!user || !user.id) {
            window.location.href = "/bayanai/auth/login";
        }
    } catch (error) {
        window.location.href = "/bayanai/auth/login";
    }
}
