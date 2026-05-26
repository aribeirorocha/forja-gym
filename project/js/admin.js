document.querySelectorAll('.role-select').forEach(select => {

    select.addEventListener('change', function () {

        const role = this.value;
        const userId = this.dataset.userId;

        fetch('admin.php', {
            method: 'POST',
            headers: {
                'Content-Type':
                    'application/x-www-form-urlencoded'
            },
            body:
                'role=' + encodeURIComponent(role) +
                '&user_id=' + encodeURIComponent(userId)
        });

    });

});