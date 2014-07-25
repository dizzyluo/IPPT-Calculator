<!DOCTYPE html>

<html>
	<head>
		<title>New IPPT Score Calculator</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

		<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
		<script src="//cdnjs.cloudflare.com/ajax/libs/angular.js/1.2.20/angular.min.js"></script>

		<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/foundation/5.3.1/css/normalize.css">
		<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/foundation/5.3.1/css/foundation.min.css">
		<script src="//cdnjs.cloudflare.com/ajax/libs/foundation/5.3.1/js/foundation.min.js"></script>
		<style>
		label {
			font-weight: bold;
		}
		label.score {
			margin-left: 10px;
		}
		html, body {
			overflow-x: hidden;
		}
		</style>
	</head>
	<body ng-app="ipptApp" style="margin-top: 20px;">
		<div class="row" ng-controller="calculatorCtrl">
			<div class="medium-6 columns medium-offset-3">
				<h3>IPPT Score Calculator</h3>

				<div class="row collapse">
					<div class="small-4 columns">	
						<label class="inline">Age</label>
					</div>
					<div class="small-4 columns left">	
						<input type="tel" ng-model="person.age" ng-change="calculate()">
					</div>
				</div>

				<div class="row collapse">
					<div class="small-12 columns">	
						<input id="commando" ng-model="person.commando" type="checkbox" ng-change="calculate()"><label for="commando">Commando / Diver / Guard</label>
					</div>
				</div>

				<div class="row collapse">
					<div class="small-4 columns">	
						<label class="inline">Sit Ups</label>
					</div>
					<div class="small-4 columns">	
						<input type="tel" ng-model="person.sitUpCount" ng-change="calculate()">
					</div>
					<div class="small-3 columns">
						<label class="inline score">{{ points.sitUp }} / 25</label>
					</div>
				</div>

				<div class="row collapse">
					<div class="small-4 columns">	
						<label class="inline">Push Ups</label>
					</div>
					<div class="small-4 columns">	
						<input type="tel" ng-model="person.pushUpCount" ng-change="calculate()">
					</div>
					<div class="small-3 columns">
						<label class="inline score">{{ points.pushUp }} / 25</label>
					</div>
				</div>

				
				<div class="row collapse">
					<div class="small-4 columns">
						<label class="inline">2.4km Run</label>	
					</div>
					<div class="small-2 columns">
						<input type="tel" placeholder="min" ng-model="person.runMin" ng-change="calculate()">
					</div>
					<div class="small-2 columns">
						<input type="tel" placeholder="sec" ng-model="person.runSec" ng-change="calculate()">
					</div>
					<div class="small-3 columns">
						<label class="inline score">{{ points.running }} / 50</label>
					</div>
				</div>

				<p ng-show="points.total > 0" class="alert-box" ng-class="{ 'Gold':'success', 'Fail': 'alert' }[points.award]">
					{{ points.total }} Points - {{ points.award }}<br ng-show="comments != ''"/>{{ comments }}
				</p>
			</div>
			<div class="row">
				<div class="small-12 medium-6 columns medium-offset-3">
					<hr>
					<small>
						Built in AngularJS by <a href="https://www.linkedin.com/in/johnldz" target="_blank">John Luo</a>. Source code on <a href="https://github.com/johnldz/IPPT-Calculator" target="_blank">GitHub</a>.<br/>
						Questions, comments? Reach me at <a href="mailto:johnldz@icloud.com">johnldz@icloud.com</a>
					</small>
				</div>
			</div>
		</div>

		<script type="text/javascript">
		var app = angular.module("ipptApp", []);

		app.controller('calculatorCtrl', ['$scope', function($scope) {

			var staticPointArray = [1,2,3,4,5,6,6,7,7,8,9,10,11,12,13,13,14,14,15,16,17,18,18,19,19,20,20,20,20,21,21,21,21,21,22,22,22,22,23,23,23,23,24,24,24,25];
			var staticRepArray = [15,14,14,13,13,13,12,12,12,11,11,11,10,10,10,9,9,9,8,8,8,7,7,7,6,6,6,5,5,5,4,4,4,3,3,3,2,2,2];

			var runPointArray = [0,1,2,4,6,8,10,12,14,16,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,35,36,36,37,37,38,38,39,39,40,40,41,42,43,44,46,48,49,50];
			var runTimeArray = [1610,1620,1620,1620,1630,1630,1630,1640,1640,1640,1650,1650,1650,1700,1700,1700,1710,1710,1710,1720,1720,1720,1730,1730,1730,1740,1740,1740,1750,1750,1750,1800,1800,1800,1810,1810,1810,1820,1820,1820];

			//
			var runSecondsArray = [];
			$scope.convertToSeconds = function(time) {
				var minutes = String(time).substr(0, 2);
				var seconds = String(time).substr(2);
				return parseInt(minutes) * 60 + parseInt(seconds)
			}

			$scope.comments = '';

			$scope.person = {
				age: null,
				sitUpCount: null,
				pushUpCount: null,
				runMin: null,
				runSec: null,
				commando: false,
			};
			$scope.points = {
				sitUp: 0,
				pushUp: 0,
				running: 0,
				total: 0,
				award: "-"
			};

			$scope.pad = function (n, width, z) {
			  z = z || '0';
			  n = n + '';
			  return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
			}
			$scope.calculate = function() {
				var p = $scope.person;
				var points = $scope.points;
				
				points.sitUp = $scope.getStaticPoints(p.age, p.sitUpCount);
				points.pushUp = $scope.getStaticPoints(p.age, p.pushUpCount);
				
				var time = $scope.pad(p.runMin,2) + '' + $scope.pad(p.runSec,2);
				points.running = $scope.getRunningPoints(p.age, time);

				points.total = points.sitUp + points.pushUp + points.running;
				points.award = $scope.getAward(points.total);

				if (points.sitUp > 0 && points.pushUp > 0 && points.running > 0) {
					mixpanel.track("Calculate", {
						age: p.age,
						commando: p.commando,
						sitUpCount: p.sitUpCount,
						sitUp: points.sitUp,
						pushUpCount: p.pushUpCount,
						pushUp: points.pushUp,
						running: points.running,
						runningTime: time,
						award: point.award
					});
				};
			}

			$scope.getAward = function (pts) {
				var gold = ($scope.person.commando) ? 85 : 81;
				
				if (pts >= gold) {
					$scope.comments = '';
					return "Gold";
				} else if (pts >= 71) {
					$scope.comments = (gold - pts) + " more points to GOLD";
					return "Silver";
				} else if (pts >= 61) {
					$scope.comments = (71 - pts) + " more points to SILVER";
					return "Pass with Incentive";
				} else if (pts >= 51) {
					$scope.comments = (61 - pts) + " more points to INCENTIVE";
					return "Pass";
				} else {
					$scope.comments = (51 - pts) + " more points to PASS";
					return "Fail";
				}
			}

			$scope.getStaticPoints = function(age, count) {
				var idx = (age - 22 < 0) ? 0 : (age - 22);
				var minReps = staticRepArray[idx];
				var pointsIdx = ((count - minReps) >= staticPointArray.length) ? staticPointArray.length - 1: count - minReps;
				return (staticPointArray[pointsIdx]) ? staticPointArray[pointsIdx] : 0;
			}

			$scope.getRunningPoints = function(age, timing) {
				var timingSeconds = $scope.convertToSeconds(timing);
				var idx = (age - 22 < 0) ? 0 : (age - 22);
				var minSeconds = $scope.convertToSeconds(runTimeArray[idx]);
				var difference = ((minSeconds - timingSeconds) < 0) ? 0 : minSeconds - timingSeconds;
				var newIdx = difference * 0.1
				if (newIdx >= runPointArray.length) {
					var newIdx = runPointArray.length -1;
				}
				return (runPointArray[Math.floor(newIdx)]) ? runPointArray[Math.floor(newIdx)] : 0;
			}
			$scope.calculate();
		}]);
		</script>
		<!-- start Mixpanel --><script type="text/javascript">(function(f,b){if(!b.__SV){var a,e,i,g;window.mixpanel=b;b._i=[];b.init=function(a,e,d){function f(b,h){var a=h.split(".");2==a.length&&(b=b[a[0]],h=a[1]);b[h]=function(){b.push([h].concat(Array.prototype.slice.call(arguments,0)))}}var c=b;"undefined"!==typeof d?c=b[d]=[]:d="mixpanel";c.people=c.people||[];c.toString=function(b){var a="mixpanel";"mixpanel"!==d&&(a+="."+d);b||(a+=" (stub)");return a};c.people.toString=function(){return c.toString(1)+".people (stub)"};i="disable track track_pageview track_links track_forms register register_once alias unregister identify name_tag set_config people.set people.set_once people.increment people.append people.track_charge people.clear_charges people.delete_user".split(" ");
		for(g=0;g<i.length;g++)f(c,i[g]);b._i.push([a,e,d])};b.__SV=1.2;a=f.createElement("script");a.type="text/javascript";a.async=!0;a.src="//cdn.mxpnl.com/libs/mixpanel-2.2.min.js";e=f.getElementsByTagName("script")[0];e.parentNode.insertBefore(a,e)}})(document,window.mixpanel||[]);
		mixpanel.init("c7e31f0de78ba726e76fc8dc57892cae");</script><!-- end Mixpanel -->
	</body>
</html>