

function postJSON(url, data) {
    const formData = new URLSearchParams();
    for (const key in data) formData.append(key, data[key]);

    return fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: formData
    }).then(res => res.json());
}

function getJSON(url) {
    return fetch(url).then(res => res.json());
}

document.addEventListener('DOMContentLoaded', function () {


    document.querySelectorAll('[data-ajax-search]').forEach(function (input) {
        let timer = null;
        input.addEventListener('input', function () {
            clearTimeout(timer);
            const endpoint = input.dataset.ajaxSearch;
            const targetSel = input.dataset.target;
            const keyword = input.value;
            timer = setTimeout(function () {
                getJSON(endpoint + '?keyword=' + encodeURIComponent(keyword))
                    .then(function (res) {
                        const target = document.querySelector(targetSel);
                        if (target && res.success) {
                            target.innerHTML = res.data.html;
                        }
                    })
                    .catch(console.error);
            }, 300);
        });
    });


    document.querySelectorAll('.ajax-status-select').forEach(function (select) {
        select.addEventListener('change', function () {
            const endpoint = select.dataset.endpoint;
            const orderId = select.dataset.orderId;
            const status = select.value;
            const badge = document.querySelector('#badge-' + orderId);

            postJSON(endpoint, { order_id: orderId, status: status, csrf_token: window.CSRF_TOKEN })
                .then(function (res) {
                    if (res.success) {
                        if (badge) {
                            badge.textContent = status;
                            badge.className = 'badge badge-' + status;
                        }
                        showToast(res.message, 'success');
                    } else {
                        showToast(res.message, 'error');
                    }
                })
                .catch(() => showToast('Network error', 'error'));
        });
    });

    document.querySelectorAll('.ajax-assign-rider').forEach(function (select) {
        select.addEventListener('change', function () {
            const orderId = select.dataset.orderId;
            postJSON('ajax/assign_rider.php', {
                order_id: orderId,
                rider_id: select.value,
                csrf_token: window.CSRF_TOKEN
            }).then(function (res) {
                showToast(res.message, res.success ? 'success' : 'error');
            });
        });
    });

    const messageForm = document.getElementById('messageForm');
    if (messageForm) {
        messageForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const input = messageForm.querySelector('[name=message]');
            if (!input.value.trim()) return;

            postJSON('ajax/send_message.php', {
                order_id: messageForm.dataset.orderId,
                receiver_id: messageForm.dataset.receiverId,
                message: input.value,
                csrf_token: window.CSRF_TOKEN
            }).then(function (res) {
                if (res.success) {
                    const chatBox = document.getElementById('chatBox');
                    const div = document.createElement('div');
                    div.className = 'chat-msg';
                    div.innerHTML = '<div class="sender">You</div><div class="text"></div>';
                    div.querySelector('.text').textContent = input.value;
                    chatBox.appendChild(div);
                    chatBox.scrollTop = chatBox.scrollHeight;
                    input.value = '';
                } else {
                    showToast(res.message, 'error');
                }
            });
        });
    }
});

function showToast(message, type) {
    let toast = document.getElementById('ajaxToast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'ajaxToast';
        toast.style.position = 'fixed';
        toast.style.bottom = '20px';
        toast.style.right = '20px';
        toast.style.padding = '12px 20px';
        toast.style.borderRadius = '8px';
        toast.style.color = '#fff';
        toast.style.zIndex = 9999;
        document.body.appendChild(toast);
    }
    toast.textContent = message;
    toast.style.background = type === 'success' ? '#4caf50' : '#d32f2f';
    toast.style.display = 'block';
    clearTimeout(toast._hideTimer);
    toast._hideTimer = setTimeout(() => { toast.style.display = 'none'; }, 2500);
}
