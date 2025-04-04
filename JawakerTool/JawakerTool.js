/*
** 2020-09 - By akhazaaleh
*/
var cardsNumbers=["A", "K", "Q", "J", 10, 9, 8, 7, 6, 5, 4, 3, 2];
var heartCards={};
var diamondCards={};
var spadeCards={};
var clubCards={};
function reset() {
    clubCards=spadeCards=diamondCards=heartCards={};
    if (document.getElementById("myStage")==null) {
        var parentStage=document.getElementsByClassName("page-content")[0].parentNode;
        var myStage=document.createElement('div');
        myStage.id="myStage";
        parentStage.appendChild(myStage);
    } else {
        document.getElementById("myStage").innerHTML="";
    }
    var myStage=document.getElementById("myStage");
    myStage.appendChild(getCards('heart'));
    myStage.appendChild(getCards('diamond'));
    myStage.appendChild(getCards('spade'));
    myStage.appendChild(getCards('club'));
}
function getCards(type) {
    var handDiv=document.createElement('div');
    handDiv.id="hand"+type;
    handDiv.className="hand card-stack ui-droppable";
    cardsNumbers.forEach(function (cardNumber){
        var cardDiv=document.createElement('div');
        cardDiv.className="card face-up "+type+"-"+cardNumber;
        var tempDiv=document.createElement('div');
        tempDiv.className="face";
        cardDiv.appendChild(tempDiv);
        handDiv.appendChild(cardDiv);
    });
    return handDiv;
}
window.setInterval(function() {
    var tableOfGame=document.getElementById("table-stack");
    if (tableOfGame!=null) {
        var addList=tableOfGame.children;
        for (var i=0 ; i<addList.length; i++)
        {
            var className=addList[i].className;
            if (className.indexOf("heart")>-1) {
                heartCards[className]=true;
                var type='heart';
            } else if (className.indexOf("diamond")>-1) {
                diamondCards[className]=true;
                var type='diamond';
            } else if (className.indexOf("spade")>-1) {
                spadeCards[className]=true;
                var type='spade';
            } else if (className.indexOf("club")>-1) {
                clubCards[className]=true;
                var type='club';
            }
            cardsNumbers.forEach(function (cardNumber){
                if (className.indexOf(type+"-"+cardNumber)>-1) {
                    var rowOfRemove=document.getElementById('hand'+type);
                    if (rowOfRemove!=null) {
                        var domCard=rowOfRemove.querySelector("."+type+"-"+cardNumber);
                        if (domCard!=null) 
                            domCard.remove();
                    }
                }
            });
        }
    }
}, 1000);
