if (document.querySelector('input[name="radio_destinataires"]')) 
{
    document.querySelectorAll('input[name="radio_destinataires"]').forEach((elem) => {
        
        elem.addEventListener("change", function(event) {
            var item = event.target.value;
            console.log(item);

            if(item == 'compte')
            { 
                document.querySelector(".compte").classList.add("active")
                document.querySelector(".promotion").classList.remove("active")
                
            } else if(item == 'promotion')
            {
                document.querySelector(".promotion").classList.add("active")
                document.querySelector(".compte").classList.remove("active")
            }
        });
    });
}

$('.flexdatalist').flexdatalist({
     searchContain: false,
     valueProperty: 'iso2',
     minLength: 1,
     focusFirstResult: true,
     selectionRequired: true,
});