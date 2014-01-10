var openRiffApp = angular.module('openRiffApp', []);

openRiffApp.controller('PlayCtrl', ['$scope', function($scope) {
    var conn = new WebSocket('ws://openriff.dev:8080');
    conn.onopen = function(e) {
        console.log("Connection established!");
    };

    conn.onmessage = function(e) {
        msg = JSON.parse(e.data);
        if(msg.kind == 'track') {
            track = msg
            if ($scope.sound) {
                $scope.sound.stop();
                $scope.sound = null;
                $scope.playing = null;
            }
            $scope.playing = track;
            SC.stream("/tracks/" + track.id, function(sound) {
                if ($scope.sound == null) {
                    sound.play();
                }
                $scope.sound = sound;
                $scope.$apply();
            });
        } else if(msg.kind == 'msg') {
            //can use to send messages like chat?
            console.log(track.message);
        } else {
            console.log('what is this message? ' + track.kind );
        }
    };

    $scope.search_results = null;
    $scope.playing = null;
    $scope.sound = null;

    $scope.search = function() {
        SC.get("/tracks", {'q': $scope.q }, function(tracks) {
            $scope.search_results = tracks;
            $scope.$apply();
        });
    };

    $scope.select = function(track) {
        console.log(track);
        conn.send(JSON.stringify(track));
        conn.send('{"kind":"msg", "message":"hello"}');
        if ($scope.sound) {
            $scope.sound.stop();
            $scope.sound = null;
            $scope.playing = null;
        }
        $scope.playing = track;
        SC.stream("/tracks/" + track.id, function(sound) {
            if ($scope.sound == null) {
                sound.play();
            }
            $scope.sound = sound;
            $scope.$apply();
        });
    };

    $scope.play = function() {
        $scope.sound.play();
    };

    $scope.pause = function() {
        $scope.sound.pause();
    };

}]);
