"use strict";

const selectedSongs = [];

const quizNameField = document.getElementById("quiz-name");
const songNameField = document.getElementById("song-name-field");
const createQuizButton = document.getElementById("create-quiz-button");
const searchSongButton = document.getElementById("search-song-button");

if (quizNameField && songNameField && createQuizButton && searchSongButton) {
    quizNameField.addEventListener("input", function(event) {
        // todo: filter & validate name, use quizNameError to notify user
        const quizNameError = document.getElementById("quiz-name-error");
        updateCreateQuizButtonStatus();
    });
    songNameField.addEventListener("input", function(event) {
        searchSongButton.disabled = !(event.target.value && event.target.value.length > 0);
    });
    searchSongButton.addEventListener("click", onSearchSongClicked);
}

function updateCreateQuizButtonStatus() {
    createQuizButton.disabled = !(quizNameField && quizNameField.value.length > 0 && selectedSongs.length > 0);
}

function onSearchSongClicked(event) {
    event.preventDefault();

    const searchResultsLabel = document.getElementById("search-results-label");
    if (!searchResultsLabel) {
        log.error('Cannot find search results label');
        return;
    }

    searchResultsLabel.style = "display: inline";
    searchResultsLabel.innerText = "Searching for song..."

    const songs = searchSpotifySongs(songNameField.value);
    if (!songs) {
        searchResultsLabel.innerText = "No song found."
        return;
    }

    searchResultsLabel.innerText = `Found ${songs.length} song result(s).`;

    const searchResults = document.getElementById("search-results");
    if (searchResults) {
        searchResults.innerHTML = ""; // clear previous search results
        searchResults.style = "display: block";
        for (const [index, song] of songs.entries()) {
            searchResults.appendChild(
                createSongWithActionTag(
                    index, song, "search-result", "V", "select-search-result", selectSongOption
                )
            );
        }
    } else {
        searchResultsLabel.style = "color: red";
        searchResultsLabel.innerText = "Cannot find search results section.";
    }
}

function createSongWithActionTag(index, song, songIdPrefix, actionText, actionIdPrefix, actionEventListener) {
    const songWithActionDiv = document.createElement("div");
    songWithActionDiv.className = "song-option";
    songWithActionDiv.id = `${songIdPrefix}-${index}`;

    const songDiv = document.createElement("div");
    songDiv.className = "song";

    const songThumbImage = document.createElement("img");
    songThumbImage.src = song.thumb;

    const songInfoDiv = document.createElement("div");
    songInfoDiv.className = "song-info";

    const songName = document.createElement("strong");
    songName.innerText = song.title || `${song.author} - ${song.name}`;
    songInfoDiv.appendChild(songName);

    songDiv.appendChild(songThumbImage);
    songDiv.appendChild(songInfoDiv);

    const songActionButton = document.createElement("button");
    songActionButton.innerText = actionText;
    songActionButton.id = `${actionIdPrefix}-${index}`;
    songActionButton.addEventListener("click", actionEventListener);

    songWithActionDiv.appendChild(songDiv);
    songWithActionDiv.appendChild(songActionButton);

    return songWithActionDiv;
}

function fetchSong(selectedSongOption) {
    const song = {};

    if (selectedSongOption) {
        const songDiv = selectedSongOption.querySelector(".song");
        if (songDiv) {
            const songThumbImage = songDiv.querySelector("img");
            song.thumb = songThumbImage && songThumbImage.src;
            const songInfoDiv = songDiv.querySelector(".song-info");
            const songTitleTag = songInfoDiv && songInfoDiv.querySelector("strong");
            song.title = songTitleTag && songTitleTag.textContent || "No author - No name";
        }
    }

    return song;
}

function rerenderSelectedSongs() {
    const selectedSongsDiv = document.getElementById("selected-songs");
    if (selectedSongsDiv) {
        selectedSongsDiv.innerHTML = "";
        for (const [index, song] of selectedSongs.entries()) {
            selectedSongsDiv.appendChild(
                createSongWithActionTag(
                    index, song, "selected-song", "X", "remove-selected-song", removeSelectedSong
                )
            );
        }
    }
}

function selectSongOption(event) {
    event.preventDefault();
    const selectSearchResultButton = event.target;
    const selectedSongOptionId = selectSearchResultButton.id.replace("select-search-result-", "search-result-");
    const song = fetchSong(document.getElementById(selectedSongOptionId));
    selectedSongs.push(song); // todo: check song id (to exclude duplicates)
    rerenderSelectedSongs();

    const searchResults = document.getElementById("search-results");
    searchResults.style = "display: none";
    const searchResultsLabel = document.getElementById("search-results-label");
    searchResultsLabel.style = "display: none";
    updateCreateQuizButtonStatus();
}

function removeSelectedSong(event) {
    event.preventDefault();
    const removeSelectedSongButton = event.target;
    let selectedSongId = removeSelectedSongButton.id.replace("remove-selected-song-", "");
    selectedSongId = selectedSongId && parseInt(selectedSongId);
    if (selectedSongId !== null) {
        selectedSongs.splice(selectedSongId, 1);
        rerenderSelectedSongs();
        updateCreateQuizButtonStatus();
    }
}

function searchSpotifySongs(songName) {
    // todo: perform song search
    return Array.of(
        {
            name: 'First song name',
            author: 'First author',
            thumb: '/img/spotify-song-pic.jpg'
        },
        {
            name: 'Second song name',
            author: 'Second author',
            thumb: '/img/spotify-song-pic.jpg'
        },
    );
}