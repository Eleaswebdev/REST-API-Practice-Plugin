document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('wp-dashboard-data');
    container.innerHTML = '<p>Loading data...</p>';
  
    const postsPromise = wp.apiFetch({ path: '/wp/v2/posts?per_page=5' });
    const usersPromise = wp.apiFetch({ path: '/wp/v2/users?per_page=5' });
    const categoriesPromise = wp.apiFetch({ path: '/wp/v2/categories?per_page=5' });
  
    Promise.all([postsPromise, usersPromise, categoriesPromise])
      .then(([posts, users, categories]) => {
        let html = '<h2>Recent Posts</h2><ul>';
        posts.forEach(post => {
          html += `<li>${post.title}</li>`;
        });
        html += '</ul>';
  
        html += '<h2>Authors</h2><ul>';
        users.forEach(user => {
          html += `<li>${user.name}</li>`;
        });
        html += '</ul>';
  
        html += '<h2>Categories</h2><ul>';
        categories.forEach(cat => {
          html += `<li>${cat.name}</li>`;
        });
        html += '</ul>';
  
        container.innerHTML = html;
      })
      .catch(error => {
        container.innerHTML = `<p style="color:red;">Error: ${error.message}</p>`;
      });
  });
  