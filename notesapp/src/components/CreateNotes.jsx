import React, { useState } from 'react'

const CreateNotes = (props) => {
    const [note, setNote] = useState({
        heading: "",
        text: ""
    });

    function handleChange(event){
        
        const {name, value} = event.target;
        setNote(prevNote => ({
            ...prevNote,
            [name]: value
        }));
    }

    function addNote(event){
        
        event.preventDefault();
        props.add(note);
        setNote({
            heading: "",
            text: ""
        });
    }

  return (
    <div>
        <form>
            <input 
            name='heading'
            onChange={handleChange}
            value={note.heading}
            placeholder='Heading'
            />

            <textarea
             name="text" 
                onChange={handleChange}
            value={note.text}
            placeholder='Write your note here'
            rows="3"
            />
            <button onClick={addNote}>+</button>
        </form>
    </div>
  );
}

export default CreateNotes