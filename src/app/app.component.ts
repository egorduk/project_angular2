import { Component } from '@angular/core';
import { Router } from '@angular/router';

@Component({ 
  moduleId: module.id,
  selector: 'app-container',
  template: '<header></header><router-outlet></router-outlet>'
})

export class AppComponent {
    constructor(public router: Router) {}
}
