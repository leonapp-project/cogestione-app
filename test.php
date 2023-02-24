<!DOCTYPE html>
<html>
  <head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.1/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
      .selectable-div {
        width: 90%;
        margin: 40px;
        border-radius: 20px;
        box-shadow: 5px 5px 10px #ccc;
        transition: all 0.2s ease-in-out;
        padding: 40px;
        position: relative;
        cursor: pointer;
      }

      .selectable-div:hover {
        background-color: #f1f1f1;
      }

      .selectable-div.selected {
        background-color: #00d1b2;
        box-shadow: 5px 5px 20px #00d1b2;
      }

      .selectable-div h2 {
        font-size: 1.5em;
        margin-bottom: 10px;
      }

      .selectable-div p {
        font-size: 0.9em;
        margin-bottom: 10px;
        width: 70%;
      }

      .selectable-div .time-indicator {
        float: right;
        font-size: 0.9em;
        color: #00d1b2;
        margin-top: -25px;
      }

      .selectable-div .availability-indicator {
        float: right;
        font-size: 0.8em;
        color: #00d1b2;
        margin-top: 5px;
        margin-right: 20px;
      }
      .is-selectable:hover {
background-color: #ADD8E6;
}

.has-shadow-right-lg {
box-shadow: 2px 2px 10px #ccc;
}

.has-margin-inside {
margin: 1.5rem;
}

.has-rounded-lg {
border-radius: 0.5rem;
}

.has-float-right {
float: right;
}

.has-text-justified {
text-align: justify;
}

.has-text-grey-light {
color: #999;
}

.has-margin-top-sm {
margin-top: 0.5rem;
}

.container {
width: 100%;
max-width: 1200px;
margin: 0 auto;
}

.box {
padding: 1rem;
border-radius: 0.5rem;
background-color: white;
}

/* Media Queries */

@media (max-width: 768px) {
.title{
font-size: 2rem;
}
.subtitle{
font-size: 1.5rem;
}
.is-size-6{
font-size: 1rem;
}
.is-size-7{
font-size: 0.8rem;
}
}

@media (max-width: 576px) {
.title{
font-size: 1.5rem;
}
.subtitle{
font-size: 1.2rem;
}
.is-size-6{
font-size: 0.9rem;
}
.is-size-7{
font-size: 0.7rem;
}
}

</style>
    </style>
  </head>
  <body>
  <div class="container">
    <div class="box has-shadow-right-lg has-margin-inside has-rounded-lg has-text-left is-selectable">
      <h1 class="title is-4">Versione con prof. Campanini</h1>
      <h2 class="subtitle is-6 has-text-right has-float-right">
        <i class="fas fa-clock"></i>
        8:00 - 9:30
      </h2>
      <p class="is-size-6 has-margin-top-sm has-text-justified">
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus. Suspendisse lectus tortor, dignissim sit amet, adipiscing nec, ultricies sed, dolor.
      </p>
      <p class="is-size-10 has-text-right has-text-grey-light has-float-right">20 posti disponibili</p>
    </div>
  </div>
</body>

<style>
.box:hover {
  background-color: #5C6AC4;
  color: white;
}
</style>