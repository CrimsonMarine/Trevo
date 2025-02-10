document.addEventListener('DOMContentLoaded', function() {
    const posts = document.querySelectorAll('.post-container1');

    posts.forEach(post => {
        const postProfile = post.querySelector('.postProfile');
        const postTitle = post.querySelector('.titlePost');
        const postContent = postProfile ? postProfile.querySelector('p') : null;

        if (postProfile) {
            postProfile.addEventListener('mouseover', function() {
                post.style.background = 'linear-gradient(to bottom, #cfcfcf, #adadad)';
                post.style.boxShadow = 'inset 0 1px 0 rgba(255,255,255,0.9), 0 2px 4px rgba(0,0,0,0.15)';

                if (postTitle) {
                    postTitle.style.borderBottom = '2px dashed rgb(246, 246, 246)';
                }
                if (postContent) {
                    postContent.style.borderBottom = '2px dashed rgb(246, 246, 246)';
                }
            });

            postProfile.addEventListener('mouseout', function() {
                post.style.background = '';
                post.style.boxShadow = '';

                if (postTitle) {
                    postTitle.style.borderBottom = '2px dashed rgb(157, 157, 157)';
                }
                if (postContent) {
                    postContent.style.borderBottom = '2px dashed rgb(157, 157, 157)';
                }
            });
        }
    });
});
