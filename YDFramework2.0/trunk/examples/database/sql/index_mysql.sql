create table Notes (
    NoteID integer not null auto_increment,
    NoteTitle varchar( 255 ),
    NoteContents text,
    primary key ( NoteID )
);
