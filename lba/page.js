const maxDays = 30;

function loadNav(number) { 
    document.querySelector(".nav").innerHTML = "";
    for(i=1; i<=maxDays; i++){
        let a = document.createElement("button");
        if(i == number) a.classList.add("active");
        a.innerHTML = i;
        a.onclick = () => {loadPage({day: a.innerHTML})};
        document.querySelector(".nav").appendChild(a);
    }
}

function loadPage({day = ''} = {}){
    console.log(day);
    fetch("http://localhost/esercizi/scraper?d="+day)
        .then((response) => response.json())
        .then((data) => {
            loadNav(data['Championship_day']);
            loadTable(data['Games']);
        });
}

function loadTable(data){
    console.log(data);
    document.querySelector(".body").innerHTML = "";
    var tbl = document.createElement('table');
    var tbdy = document.createElement('tbody');
    for (i = 0; i < data.length; i++) {
        if(i == 0){
            var tr = document.createElement('tr');
            for(const [key, value] of Object.entries(data[i])){
                var th = document.createElement('th');
                th.innerHTML = key;
                tr.appendChild(th);
            }
            tbdy.appendChild(tr);
        }
        var tr = document.createElement('tr');
        for(const [key, value] of Object.entries(data[i])){
            var td = document.createElement('td');
            if(key!= 'info'){
                td.innerHTML = value;
            }else{
                let btn = document.createElement('button');
                btn.onclick = () => {loadMatchDetail(value)};
                btn.innerHTML = '&#8617;';
                td.appendChild(btn); 
            }
            tr.appendChild(td);
        }
        tbdy.appendChild(tr);
    }
    tbl.appendChild(tbdy);
    document.querySelector(".body").appendChild(tbl);
}

function loadMatchDetail(squads) {
    console.log(squads);
}