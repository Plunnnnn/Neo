function searchMovies() {
    var movieTitle = document.getElementById("movieTitle").value;
   
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {            //Places les resulat ans le ag results
            var resultsDiv = document.getElementById("results");
            resultsDiv.innerHTML = xhr.responseText;
        }
    };
    
    xhr.open("GET", "search.php?title=" + encodeURIComponent(movieTitle), true);
    xhr.send();
}

function showMovieInfo(movieId, lecteurLink) {
   
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var resultsDiv = document.getElementById("results");
            resultsDiv.innerHTML = xhr.responseText;

            
            var videoPlayer = document.getElementById("videoPlayer");  //places les infos pour lire ue video
            videoPlayer.src = lecteurLink;
            videoPlayer.style.display = "block";
        }
    };
    
    xhr.open("GET", "search.php?movie_id=" + encodeURIComponent(movieId), true);
    xhr.send();
}
function redirectToPlayer(movieID) {
    
    const playerURL = `player.html?movieID=${movieID}`;

    
    window.location.href = playerURL;
}

function performAjaxRequest() {
    $.ajax({
        type: 'POST',
        url: 'login.php', 
        dataType: 'json',
        success: function(response) {
            
            if (response.status === 'success') {
                
            } else {
                
                console.error('Échec de l\'opération :', response.message);
            }
        },
        
    });
}
