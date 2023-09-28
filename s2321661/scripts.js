function keep_range(form,targets){
    // keep selection of range elecment 
    for(let key in form){
        if(targets.indexOf(key) != -1){  //'targets' contains key
            document.getElementById(key).value = form[key];
        }
    }
}

function keep_edge(form,targets){
    // keep selection of the interval edge
    for(let i in targets){
        var value = targets[i];
        if(!form[value]){  // if can not find i in form
            document.getElementById(value).checked = false;
        }
    }  
}

function download_csv(csv_str){
    // this function accept a string that stores csv content. (Ref: https://www.geeksforgeeks.org/how-to-export-html-table-to-csv-using-javascript/)
    csv_obj = new Blob([csv_str], {type: "text/csv"});  // Create Blob object
    var temp_link = document.createElement('a');  // Create to temporary link to trigger download process
    temp_link.download = "table.csv";  // set download file name
    temp_link.href = URL.createObjectURL(csv_obj);  // create a url for this Blob object
    temp_link.style.display = "none";  // hide the temporary link
    document.body.appendChild(temp_link);  //insert the link to body
    temp_link.click();
    document.body.removeChild(temp_link);  // delete temporary link
}

function table_to_csv(table_id, sep=',') {
    // this function can turn result table into a csv file (Ref: https://www.geeksforgeeks.org/how-to-export-html-table-to-csv-using-javascript/)
    var csv_data = [];
    let table = document.querySelector(table_id);  // set target table as document instance 
    var rows = table.getElementsByTagName('tr');  
    for (let i = 0; i < rows.length; i++) {  // get data in header                   
        var headers = rows[i].querySelectorAll('th,td');  // get every objects in <th>
        var row = []
        for (let j = 0; j < headers.length; j++) {
            var header = headers[j].innerText;
            row.push(header);
        }
        csv_data.push(row.join(sep));  // a string that combines each element in <th> with seperator (',') 
    }
    csv_data = csv_data.join('\n');  // compact into one string
    download_csv(csv_data);
}





