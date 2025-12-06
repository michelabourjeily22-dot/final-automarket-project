document.addEventListener('DOMContentLoaded', function() {

    document.querySelectorAll('.save-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const carId = this.getAttribute('data-car-id');
            const isSaved = this.classList.contains('saved');

            fetch('api/save_listing.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    car_id: carId,
                    action: isSaved ? 'unsave' : 'save'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.action === 'saved') {
                        this.classList.add('saved');
                        this.querySelector('svg').setAttribute('fill', 'currentColor');
                    } else {
                        this.classList.remove('saved');
                        this.querySelector('svg').setAttribute('fill', 'none');
                    }
                } else {
                    if (data.error === 'not_logged_in') {
                        window.location.href = 'login.php';
                    } else {
                        alert('Error: ' + (data.error || 'Failed to save listing'));
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        });
    });

    document.querySelectorAll('.share-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const carId = this.getAttribute('data-car-id');
            const carUrl = this.getAttribute('data-car-url');
            const carTitle = this.getAttribute('data-car-title');

            window.shareData = {
                url: carUrl,
                title: carTitle
            };

            const shareModal = document.getElementById('shareModal');
            if (shareModal) {
                shareModal.style.display = 'flex';
            }
        });
    });

    document.querySelectorAll('.share-option').forEach(btn => {
        btn.addEventListener('click', function() {
            const platform = this.getAttribute('data-platform');
            const shareData = window.shareData || {};
            const url = encodeURIComponent(shareData.url || window.location.href);
            const title = encodeURIComponent(shareData.title || document.title);
            const text = encodeURIComponent(shareData.title || 'Check out this car listing!');

            let shareUrl = '';

            switch(platform) {
                case 'whatsapp':
                    shareUrl = `https://wa.me/?text=${text}%20${url}`;
                    window.open(shareUrl, '_blank');
                    break;
                case 'facebook':
                    shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
                    window.open(shareUrl, '_blank', 'width=600,height=400');
                    break;
                case 'twitter':
                    shareUrl = `https://twitter.com/intent/tweet?text=${text}&url=${url}`;
                    window.open(shareUrl, '_blank', 'width=600,height=400');
                    break;
                case 'messenger':
                    shareUrl = `https://www.facebook.com/dialog/send?link=${url}&app_id=YOUR_APP_ID`;
                    window.open(shareUrl, '_blank', 'width=600,height=400');
                    break;
                case 'copy':
                    navigator.clipboard.writeText(shareData.url || window.location.href).then(() => {
                        alert('Link copied to clipboard!');
                    }).catch(() => {

                        const textarea = document.createElement('textarea');
                        textarea.value = shareData.url || window.location.href;
                        document.body.appendChild(textarea);
                        textarea.select();
                        document.execCommand('copy');
                        document.body.removeChild(textarea);
                        alert('Link copied to clipboard!');
                    });
                    break;
            }

            closeShareModal();
        });
    });

    document.querySelectorAll('.comment-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const carId = this.getAttribute('data-car-id');

            window.location.href = `car_detail.php?id=${carId}#comments`;
        });
    });

    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation(); 

            const carId = this.getAttribute('data-car-id');
            const postElement = this.closest('.feed-post');

            if (!confirm('Are you sure you want to delete this listing? This action cannot be undone.')) {
                return;
            }

            this.disabled = true;
            this.style.opacity = '0.5';

            fetch('api/delete_listing.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    car_id: carId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {

                    if (postElement) {
                        postElement.style.transition = 'opacity 0.3s, transform 0.3s';
                        postElement.style.opacity = '0';
                        postElement.style.transform = 'scale(0.95)';
                        setTimeout(() => {
                            postElement.remove();

                            const feedMain = document.querySelector('.feed-main');
                            if (feedMain && feedMain.querySelectorAll('.feed-post').length === 0) {
                                feedMain.innerHTML = `
                                    <div class="empty-feed">
                                        <div class="empty-icon">🚗</div>
                                        <h2>No listings yet</h2>
                                        <p>Be the first to post a car!</p>
                                        <a href="add_post.php" class="btn-primary" style="text-decoration: none; display: inline-block;">Post Your First Car</a>
                                    </div>
                                `;
                            }
                        }, 300);
                    }
                } else {
                    this.disabled = false;
                    this.style.opacity = '1';
                    alert('Error: ' + (data.error || 'Failed to delete listing'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                this.disabled = false;
                this.style.opacity = '1';
                alert('An error occurred. Please try again.');
            });
        });
    });
});

function closeShareModal() {
    const shareModal = document.getElementById('shareModal');
    if (shareModal) {
        shareModal.style.display = 'none';
    }
}

window.addEventListener('click', function(event) {
    const shareModal = document.getElementById('shareModal');
    if (event.target === shareModal) {
        closeShareModal();
    }
});

document.addEventListener("DOMContentLoaded", () => {

    document.querySelectorAll('.comment-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const carId = this.getAttribute('data-car-id');
            window.location.href = `car_detail.php?id=${carId}#comments`;
        });
    });

});

function escapeHtml(str) {
  if (!str && str !== 0) return '';
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}

