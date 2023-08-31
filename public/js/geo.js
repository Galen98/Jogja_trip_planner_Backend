
//const Geo = require('geo-nearby');
import Geo from 'geo-nearby';
const { Script } = require('vm');


const dataSet = [
  { i: 'Perth',     g: 3149853951719405 },
  { i: 'Adelaide',  g: 3243323516150966 },
  { i: 'Melbourne', g: 3244523307653507 },
  { i: 'Canberra',  g: 3251896081369449 },
  { i: 'Sydney',    g: 3252342838034651 },
  { i: 'Brisbane',  g: 3270013708086451 },
  { i: 'Sydney',    g: 3252342838034651 }
];

const geo = new Geo(dataSet);

geo.nearBy(-33.87, 151.2, 5000); 