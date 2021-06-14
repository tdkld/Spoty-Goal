<?php include_once('templates/header.php'); ?>
<main class="content">
    <p class=title>Create custom quiz</p>
    <form method="post" action="/quiz.php" class="form pure-form pure-form-stacked">
        <label for="quiz-name">Name</label>
        <span class="pure-form-message" id="quiz-name-error" style="display: none">This is a required field.</span>
        <input type="text" name="quiz-name" id="quiz-name" required>
        <label>Search for song</label>
        <div class="search">
            <input type="text" name="search" id="song-name-field">
            <input type="button" value="Search" id="search-song-button" disabled>
        </div>
        <label id="search-results-label" for="search-results" style="display: none">Search results</label>
        <div class="scrollable" id="search-results" style="display: none">
            <!-- TODO: Remove example -->
            <!-- <div class="song-option" id="search-result-1">
                <div class="song">
                    <img src="/img/spotify-song-pic.jpg">
                    <div class="song-info">
                        <strong>Artist Name - Spotify Song Name</strong>
                        <p>playable part</p>
                    </div>
                </div>
                <button id="select-search-result-1">V</button>
            </div> -->
        </div>
        <label for="selected-songs">Selected songs</label>
        <div class="scrollable" id="selected-songs">
            <!-- TODO: Remove example -->
            <!-- <div class="song-option" id="selected-song-1">
                <div class="song">
                    <img src="/img/spotify-song-pic.jpg">
                    <div class="song-info">
                        <strong>Artist Name - Spotify Song Name</strong>
                        <p>playable part</p>
                    </div>
                </div>
                <button id="remove-selected-song-1">X</button>
            </div> -->
        </div>
        <input type="submit" value="Create quiz" class="pure-button success" id="create-quiz-button" disabled>
    </form>
</main>
<script defer src="/js/quiz.js"></script>
<?php include_once('templates/footer.php'); ?>