import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ProcessoListComponent } from './processo-list.component';
import { ProcessoListRoutingModule } from './processo-list-routing.module';
import { FormsModule } from '@angular/forms';
import { SharedModule } from '../../shared/shared.module';


@NgModule({
  imports: [
    CommonModule,
    ProcessoListRoutingModule,
    FormsModule,
    SharedModule
  ],
  declarations: [
    ProcessoListComponent
  ]
})
export class ProcessoListModule { }
