import { NgModule }      from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { FormsModule } from '@angular/forms';
import { HttpModule } from '@angular/http';

import { routing } from './app.routes';

import { AppComponent }  from './app.component';

import { DataService } from './common/service/data.service';
import { SafeBgPipe } from './common/pipe/safe.pipe';
import { AlertComponent } from 'ng2-bootstrap/components/alert';

@NgModule({
    imports: [ BrowserModule, routing, FormsModule, HttpModule],
    declarations: [ AppComponent, SafeBgPipe, AlertComponent ],
    bootstrap: [ AppComponent ],
    providers: [ DataService ]
})
export class AppModule { }