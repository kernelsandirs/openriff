var openRiffApp = angular.module('openRiffApp', []);

openRiffApp.controller('PlayCtrl', ['$scope', function($scope) {
    $scope.search_results = null;
    $scope.playing = null;
    $scope.sound = null;

    $scope.queue = [];

    $scope.search = function() {
        SC.get("/tracks", {'q': $scope.q, 'filter': 'streamable'}, function(tracks) {
            $scope.search_results = tracks;
            $scope.$apply();
        });
    };

    $scope.select = function(track) {
        console.log(track);
        $scope.queue.push(track);

        if ($scope.queue.length == 1 && $scope.playing == null) {
            $scope.playNext();
        }
    };

    $scope.toggleMute = function() {
        $scope.sound.toggleMute();
    };

    $scope.setVolume = function(level) {
        $scope.sound.setVolume(level);
    };

    $scope.voteDown = function(track) {
        console.log('downvote');
        console.log(track);
        if (track == $scope.playing) {
            $scope.playNext();
        } else {
            for (var i = $scope.queue.length; i--;) {
                if ($scope.queue[i] === track) {
                    $scope.queue.splice(i, 1);
                    break;
                }
            }
        }
    };

    $scope.playNext = function() {
        var isMuted;
        var volume;
        if ($scope.sound) {
            isMuted = $scope.sound.muted;
            volume  = $scope.sound.volume;
            $scope.sound.stop();
        }
        if ($scope.queue.length > 0) {
            $scope.playing = $scope.queue.shift();
            console.log($scope.playing);

            SC.stream("/tracks/" + $scope.playing.id, function(sound) {
                sound.play({
                    whileplaying: function() {
                        $scope.$apply();
                    },
                    onfinish: function() {
                        $scope.playNext();
                    }
                });
                $scope.sound = sound;
                if(isMuted == 1){
                    $scope.sound.mute();
                }
                $scope.sound.setVolume(volume);
            });

        }

    }
}]);
