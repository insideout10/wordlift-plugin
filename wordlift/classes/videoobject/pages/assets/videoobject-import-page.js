window.addEventListener("load", function () {
    const startBtn = document.getElementById("wl-start-btn");
    const stopBtn = document.getElementById("wl-stop-btn");
    const {restUrl, nonce} = _wlVideoObjectImportSettings

    const updateProgressBar = function () {
        fetch(restUrl + "wordlift/v1/videos/background/get_state", {
            method: "POST",
            headers: {"X-WP-Nonce": nonce }
        })
            .then(response => response.json())
            .catch(() => {
            })
            .then((data) => {
                let percentage = (data.index * 100.0) / data.count;
                if (percentage > 100) {
                    percentage = 100
                }
                document.querySelector(".wl-task__progress__bar").style.width =
                    percentage + "%";

                if ("started" === data.state) {
                    if (!startBtn.classList.contains("hidden"))
                        startBtn.classList.add("hidden");
                    if (stopBtn.classList.contains("hidden"))
                        stopBtn.classList.remove("hidden");
                } else {
                    if (startBtn.classList.contains("hidden"))
                        startBtn.classList.remove("hidden");
                    if (!stopBtn.classList.contains("hidden"))
                        stopBtn.classList.add("hidden");
                }

                setTimeout(updateProgressBar, 1000);
            });
    };

    setTimeout(updateProgressBar, 1000);

    startBtn.addEventListener("click", function () {
        fetch(restUrl + "wordlift/v1/videos/background/start", {
            method: "POST",
            headers: {"X-WP-Nonce": nonce }
        }).then(() => {
        });
    });

    stopBtn.addEventListener("click", function () {
        fetch(restUrl + "wordlift/v1/videos/background/stop", {
            method: "POST",
            headers: {"X-WP-Nonce": nonce }
        }).then(() => {
        });
    });
});
