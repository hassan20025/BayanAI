export async function redirectIfNotLoggedIn() {
    try {
        const response = await fetch("http://localhost/BayanAI/api/users/login.php", {
            credentials: "include",
        });

        console.log(await response.json());
        if (!response.ok) {

            throw new Error("Not authenticated");
        }

        const user = await response.json();

        if (!user || !user.id) {
            // window.location.href = "/temp/BayanAI/auth/login";
        }
    } catch (error) {
        // window.location.href = "/temp/BayanAI/auth/login";
    }
}
