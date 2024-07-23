Tracking detail including
1. Session information
2. User interaction with video
3. User eye tracking

## Session Information:
| Field | Description | 
|----------|----------|
| id    |  String: The video session ID     | 
| os    |  String: The operating system of user     | 
| browser    |  String: The browser that user is using     | 
| doc_width    | Integer: The width of the whole document (including scroll)     | 
| doc_height    | Integer: The height of the whole document (including scroll)     | 
| init_screen_width    | Integer: The width of the browser window     | 
| init_screen_height    | Integer: The height of the browser window     | 
| layout    | String: Flag represent layout or UI sections (V=>video, A=>annotation,C=>comment)(e.g. VAC means the layout contains both video, annotation, and comment sections)     |

## Eye tracking:
| Field | Description | 
|----------|----------|
| x    |  Float: The x coordinate predict, where the eye is looking at     | 
| y    |  Float: The y coordinate predict, where the eye is looking at     | 
| id    | String, tag: The video session ID     | 
| gv_id    | Integer, tag: The group video ID     | 
| target    | String, tag: The component predict, where the eye is looking at (V,A,C,O,N)(O  is for other, N is for Not on screen)     | 
| timestamp    |  Integer: The UNIX time in milisecond where this is recorded    |

- Eye tracking every 0.5 seconds

## User Interaction:
| Field          | Description                                                                 |
|----------------|-----------------------------------------------------------------------------|
| id             | Id of tracking record                                                       |
| group_video_id | Id of group video where the tracking is recorded                            |
| user_id        | Id of user                                                                  |
| event          | The interaction (fullscreen, pause video, add comment, etc.)                |
| target         | The element that user interact with                                         |
| video_time     | Current video time                                                          |
| info           | Additional information                                                      |
| event_time     | Integer: The UNIX time in milliseconds where this is recorded               |
| ref_id         | Database reference id of the target that user interact with                 |
| ref_type       | Database reference type (comments, annotations, etc.) of the target         |
| session_id     | Id of the session information record mentioned above                        |
