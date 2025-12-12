import React , {useState} from 'react'
import Header from "./components/Header"
import Note from "./components/Note"
import CreateNotes from "./components/CreateNotes"

const App = () => {
  const [notes, setNotes] = useState([]);

  function addNote(newNote){
    setNotes(oldNotes =>{
      return [...oldNotes, newNote];
    });
  }

  function removeNote(id){
    setNotes(oldNotes => {
      return oldNotes.filter((noteContent, serial) => {
        return serial !== id;
      });
    });
  }


  return (
  <div>
    <Header/>
    <CreateNotes add={addNote} />
    {notes.map((noteContent, serial) =>{
      return (
        <Note 
        key={serial}
        id={serial}
        heading={noteContent.heading}
        text={noteContent.text}
        onDelete={removeNote}
        />
      );
    })}
  </div>  
  );
}

export default App