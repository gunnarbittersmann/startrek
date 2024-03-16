function onYouTubeIframeAPIReady() {
	const videos = [];
	const iframes = document.querySelectorAll('iframe');
	
	for (let iframe of iframes) {
		videos.push({ player: new YT.Player(iframe.id), offset: parseFloat(iframe.dataset.offset || 0) });
	}
	
	videos.SKIPTIME = 5;
	
	videos.mute = function () {
		videos.forEach((video, index) => {
			if (index == 0) {
				video.player.unMute();
			}
			else {
				video.player.mute();
			}
		});
	};
	
	videos.seekTo = function (position) {
		for (let video of videos) {
			video.player.seekTo(position + (video.offset || 0));
		}
	}
	
	videos.play = function () {
		for (let video of videos) {
			video.player.playVideo();
		}
	}
	
	videos.pause = function () {
		for (let video of videos) {
			video.player.pauseVideo();
		}
	}
	
	videos.getCurrentTime = function () {
		return videos[0].player.getCurrentTime();
	}
	
	videos.skip = function (offset) {
		const destinationTime = videos.getCurrentTime() + offset;
		videos.seekTo(destinationTime);
	}

	document.querySelector('#start-pause').addEventListener('click', event => {
		switch (videos[0].player.getPlayerState()) {
			default:
				videos.mute();
				videos.seekTo(0);
				// fall thru
			case 2: // paused
				videos.play();
				break;

			case 1: // playing
				videos.pause();
		}
	});

	document.querySelector('#rewind').addEventListener('click', event => {
		videos.skip(-videos.SKIPTIME);
	});

	document.querySelector('#forward').addEventListener('click', event => {
		videos.skip(videos.SKIPTIME);
	});
}
