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
    fetch("http://localhost/rovoletto/scraper?d="+day)
        .then((response) => response.json())
        .then((data) => {
            loadNav(data['Championship_day']);
            loadTable(data['Games']);
        });
}

function loadTable(data){
    document.querySelector(".body").innerHTML = "";
    document.querySelector(".detail").innerHTML = "";
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
    document.querySelector(".detail").innerHTML = "";
    var tbl = document.createElement('table');
    var tbdy = document.createElement('tbody');
    var tr = document.createElement('tr');
    var td1 = document.createElement('td');
    fetch("http://localhost/rovoletto/scraper/detail.php?d="+squads['Squadra di casa'])
        .then((response) => response.json())
        .then((data) => {
            td1.appendChild(buildSquadTable(data)); 
        });   
    tr.appendChild(td1);
    var td2 = document.createElement('td');
    fetch("http://localhost/rovoletto/scraper/detail.php?d="+squads['Squadra ospite'])
        .then((response) => response.json())
        .then((data) => {
            td2.appendChild(buildSquadTable(data)); 
        });   
    tr.appendChild(td2);
    tbdy.appendChild(tr);
    tbl.appendChild(tbdy);
    document.querySelector(".detail").appendChild(tbl);
}

function buildSquadTable(data) {
    console.log(data);
    var tbl = document.createElement('table');
    var tbdy = document.createElement('tbody');

    //logo
    var logo = document.createElement('tr');
    var a = document.createElement('th');
    a.colSpan = 8;
    var img = document.createElement('img');
    img.src = data['Logo'];
    img.id = 'logo';
    a.appendChild(img);
    logo.appendChild(a);
    tbdy.appendChild(logo);

    //nome squadra
    var squad = document.createElement('tr');
    var b = document.createElement('th');
    b.colSpan = 8;
    b.innerHTML = data['Name'];
    squad.appendChild(b);
    tbdy.appendChild(squad);

    //allenatore
    var allenatore = document.createElement('tr');
    var c = document.createElement('th');
    var d = document.createElement('th');
    c.colSpan = 4;
    d.colSpan = 4;
    var ico = document.createElement('img');
    ico.src = data['Allenatore']['Displayed_icon'];
    d.innerHTML = data['Allenatore']['Name'];
    c.appendChild(ico);
    allenatore.appendChild(c);
    allenatore.appendChild(d);
    tbdy.appendChild(allenatore);

    for (i = 0; i < data['Roster'].length; i++) {
        if(i == 0){
            var tr = document.createElement('tr');
            for(const [key, value] of Object.entries(data['Roster'][i])){
                var th = document.createElement('th');
                if(key == 'Displayed_icon')
                    th.innerHTML = ' ';
                else
                    th.innerHTML = key;
                tr.appendChild(th);
            }
            tbdy.appendChild(tr);
        }
        var tr = document.createElement('tr');
        for(const [key, value] of Object.entries(data['Roster'][i])){
            var td = document.createElement('td');
            if(key!= 'Displayed_icon'){
                td.innerHTML = value;
            }else{
                let icon = document.createElement('img');
                icon.src = value;
                icon.id = 'player';
                td.appendChild(icon); 
            }
            tr.appendChild(td);
        }
        tbdy.appendChild(tr);
    }
    
    tbl.appendChild(tbdy);
    return tbl;
}