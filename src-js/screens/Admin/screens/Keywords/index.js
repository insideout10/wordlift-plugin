import React from 'react';
import ReactDOM from 'react-dom';
import Keywords from './Keywords';

const dataSource = [{
	"keyword": "WordLift",
	"trend": "POSITIVE",
	"rank": 1,
	"volume": 100,
	"urls": [
			"http://example.org/2",
			"http://example.org/N"
	]
}, {
	"keyword": "WordLift",
	"trend": "NEGATIVE",
	"rank": 2,
	"volume": 48,
	"urls": [
			"http://example.org/1",
			"http://example.org/2",
			"http://example.org/N"
	]
}, {
	"keyword": "WordLift",
	"trend": "POSITIVE",
	"rank": 3,
	"volume": 30,
	"urls": [
			"http://example.org/2",
			"http://example.org/N"
	]
}, {
	"keyword": "WordLift",
	"trend": "NEGATIVE",
	"rank": 4,
	"volume": 2203,
	"urls": [
			"http://example.org/1",
			"http://example.org/2",
			"http://example.org/N"
	]
}, {
	"keyword": "WordLift",
	"trend": "POSITIVE",
	"rank": 5,
	"volume": 5,
	"urls": [
			"http://example.org/2",
			"http://example.org/N"
	]
}, {
	"keyword": "WordLift",
	"trend": "NEGATIVE",
	"rank": 123,
	"volume": 123,
	"urls": [
			"http://example.org/1",
			"http://example.org/2",
			"http://example.org/N"
	]
}];

ReactDOM.render(
	<Keywords data={dataSource} />,
	document.getElementById('wl-keywords-table')
);
