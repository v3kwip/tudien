Dictionary
====

Dictionary that helps me study English grammar, vocabulary, …

Being developed.

## Data

- Word: type,

## Backends

- [x] `GET /topic/index[/{parentTopicId}]` List of topics
- [x] `GET /topc/{TopicId}` Load topic details, list of words.
- [x] `POST /topic/edge/{word}/{type}` Visit/know a word, increase value if revisit.
- [x] `POST /word/edge/{word}/{type}` Visit/know a word, increase value if revisit.
- [ ] `GET /word/edge/{word}` Load a word details.

## Tasks

- [ ] Front-end for topic index
- [ ] Front-end for topic details page.
- [ ] Crawl word meanings: pronounce, synonyms, picture, examples, …
- [ ] Word details page
  - [ ] Flag word as known
  - [ ] Job to expire old words if not reviewed
- [ ] Word review page
- [ ] Crawl grammars
- [ ] Multiple users
