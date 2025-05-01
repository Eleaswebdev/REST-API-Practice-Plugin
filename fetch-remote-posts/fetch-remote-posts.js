document.addEventListener("DOMContentLoaded", function () {
    const btn = document.getElementById("load-external-posts");
    const container = document.getElementById("external-posts-container");

    if (!btn) return;

    btn.addEventListener("click", function () {
        fetch(fetchPosts.endpoint)
            .then((res) => res.json())
            .then((data) => {
                data.forEach((post) => {
                    console.log(post);
                    const div = document.createElement("div");
                    div.classList.add("remote-post");
                    div.innerHTML = `<h2>${post.title}</h2><p>${post.body}</p>`;
                    container.appendChild(div);
                });
                btn.disabled = true;
                btn.innerText = "Posts Loaded";
            })
            .catch((err) => {
                console.error("Error:", err);
                btn.innerText = "Failed to Load";
            });
    });
});
