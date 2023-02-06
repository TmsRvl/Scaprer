const maxDays = 30;

function loadNav(number) {
    console.log('aaaaa');
    document.querySelector(".nav").innerHTML = "";
    for(i=1; i<maxDays; i++){
        let a = document.createElement("button");
        a.onclick = "loadPage({day:"+i+"})";
        a.innerHTML = i;
        document.querySelector(".nav").appendChild(a);
    }
}

function loadPage({day = ''} = {}){
    fetch("http://localhost/rovoletto/scraper?d="+day)
        .then((response) => response.json())
        .then((data) => {
            loadNav(data['Championship_day']);
        });
}