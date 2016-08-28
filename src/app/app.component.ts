import { Component } from '@angular/core';
import { Router, ROUTER_DIRECTIVES } from '@angular/router';
import { APP_PROVIDERS } from './app.providers';
import { HeaderComponent } from './header/header.component';

@Component({ 
  moduleId: module.id,
  selector: 'app-container',
  template: '<header></header><router-outlet></router-outlet>',
  directives: [ ROUTER_DIRECTIVES, HeaderComponent ],
  providers: [ APP_PROVIDERS ]
})

export class AppComponent {
    constructor(public router: Router) {}
}
