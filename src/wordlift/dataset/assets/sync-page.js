window.addEventListener('load', function () {

    const updateProgressBar = function () {
        wp.apiRequest({"path": "wordlift/v1/dataset/info"}).success(data => {
            document.querySelector('.wl-task__progress__bar')
                .style.width = (data.index * 100.0 / data.count) + '%';

            setTimeout(updateProgressBar, 1000);
        });
    };

    setTimeout(updateProgressBar, 1000);

    document.getElementById('wl-start-btn')
        .addEventListener('click', function() {
            wp.apiRequest({"method": "POST", "path": "wordlift/v1/dataset/sync"})
        })

});