import './App.css';
import TextField from '@mui/material/TextField';
import Button from '@mui/material/Button';
import { DataGrid } from '@mui/x-data-grid';
import React, { useState, useEffect } from 'react';
import axios from "axios";

function App() {
  
  const [ico, setIco] = useState("")
  const [name, setName] = useState("")
  const [check, setCheck] = useState(true)
  const [rows, setRows] = useState([])
  const columns = [
    { field: 'id', headerName: 'IČO', flex:1 },
    { field: 'Meno', headerName: 'Jméno', flex:3},
  ];

  const handleSubmit = async e => {
    e.preventDefault();
    var bodyFormData = new FormData();
    bodyFormData.append('ico', ico);
    bodyFormData.append('name', name);
    bodyFormData.append('action', "submit");
    axios({
      method: "post",
      url: "http://localhost:8000/submit",
      data: bodyFormData,
      headers: { "Content-Type": "multipart/form-data" },
    })
      .then(function (response) {
        console.log(response.data);
        if(response.data === "Found nothing") alert ("Found no entries");
        else setRows(response.data);
      })
      .catch(function (response) {
        //handle error
        console.log(response);
      });
  }

  const handleSave = async e => {
    var bodyFormData = new FormData();
    bodyFormData.append('ico', e.row.id);
    bodyFormData.append('name', e.row.Meno);
    bodyFormData.append('action', "save");
    axios({
      method: "post",
      url: "http://localhost:8000/submit",
      data: bodyFormData,
      headers: { "Content-Type": "multipart/form-data" },
    })
      .then(function (response) {
        if (response.data === "bool(false)") alert ("Error while saving to DB");
        else alert("Successfuly added entry to our DB");
      })
      .catch(function (response) {
        //handle error
        console.log(response);
      });
  }

  useEffect(() => {
    if (ico.match(/^[0-9]+$/) || ico === "") {
      setCheck(true)
    }
    else setCheck(false)
  }, [ico]);

  return (
    <div className="App">
      <header className="App-header">
        <form
          action="http://localhost:8000/"
          method="post"
          onSubmit={(e) => handleSubmit(e)}
        >
          <TextField 
            name="ico" 
            label="IČO" 
            InputLabelProps={{style: { color: '#fff' },}}
            variant="standard" 
            onChange={e => setIco(e.target.value)} 
            value={ico} 
            sx={{ input: { color: '#fff' } }} 
            helperText={check ? "" : "Zadejte jen číselné hodnoty"}
            error={!check}
          />
          <TextField 
            name="name" 
            label="Jméno firmy" 
            InputLabelProps={{style: { color: '#fff' },}}
            variant="standard" 
            onChange={e => setName(e.target.value)} 
            value={name} 
            sx={{ input: { color: '#fff' } }}
          />
          <Button variant="contained" type='submit'>Vyhledat</Button>
        </form>
        
        <DataGrid
          rows={rows}
          columns={columns}
          onRowDoubleClick={(params) => handleSave(params)}
          sx={{
            margin:20,
            width:800,
            boxShadow: 2,
            border: 2,
            borderColor: 'primary.light',
            '& .MuiDataGrid-row:hover': {
              color: 'primary.main',
            },
            ' & .MuiDataGrid-columnHeader': {
              color:'white',
            },
            ' & .MuiDataGrid-row': {
              color:'white',
            }
          }}
        />
        
      </header>
    </div>
  );
}

export default App;
