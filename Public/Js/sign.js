document.getElementById("privelege").addEventListener("change", e => {
    var choix = e.target.value

    console.log(choix)

    var etd = document.querySelector(".form-group.etd")
    var esn = document.querySelector(".form-group.esn")

    if(choix == 'enseignant' )
    {
        esn.classList.add("active")
        etd.classList.remove("active")
    }
    else if(choix == 'etudiant')
    {
        etd.classList.add("active")
        esn.classList.remove("active")  
    }
})