export async function redirectIfNotLoggedIn() {
    try {
        console.log("asdad")
        const response = await fetch("http://localhost/BayanAI/api/users/me.php", {
            credentials: "include",
        });

        // console.log(await response.json());
        if (!response.ok) {

            throw new Error("Not authenticated");
        }

        const user = await response.json();
        console.log(user);
        if (!user || !user.data.id) {
            window.location.href = "/BayanAI/auth/login";
        }
    } catch (error) {
        console.log(error);
        // window.location.href = "/BayanAI/auth/login";
    }
}
window.redirectIfNotLoggedIn = redirectIfNotLoggedIn;
redirectIfNotLoggedIn();