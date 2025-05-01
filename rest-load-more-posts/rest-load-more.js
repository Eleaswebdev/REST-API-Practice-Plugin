let currentPage = 1;
const postsContainer = document.getElementById('rest-posts-container');
const loadMoreBtn = document.getElementById('rest-load-more');

function renderPost(post) {
    console.log(post);
    return `<div class="rest-post">
        <h2>${post.title}</h2>
        <div>${post.excerpt.rendered}</div>
    </div>`;
}

function loadPosts() {
    fetch(`${RestLoadMoreSettings.rest_url}?page=${currentPage}&per_page=5&_fields=title,excerpt`, {
        headers: {
            'X-WP-Nonce': RestLoadMoreSettings.nonce
        }
    })
    .then(response => {
        if (!response.ok) throw new Error("No more posts");
        return response.json();
    })
    .then(posts => {
        if (posts.length === 0) {
            loadMoreBtn.disabled = true;
            loadMoreBtn.innerText = "No More Posts";
        } else {
            posts.forEach(post => {
                postsContainer.innerHTML += renderPost(post);
            });
        }
    })
    .catch(() => {
        loadMoreBtn.disabled = true;
        loadMoreBtn.innerText = "No More Posts";
    });
}

document.addEventListener('DOMContentLoaded', function () {
    loadPosts(); // Load initial posts
    currentPage++;

    loadMoreBtn.addEventListener('click', function () {
        loadPosts();
        currentPage++;
    });
});


