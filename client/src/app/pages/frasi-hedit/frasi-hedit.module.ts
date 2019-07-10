import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FrasiHEditComponent } from './frasi-hedit.component';
import { FrasiHEditRoutingModule } from './frasi-hedit-routing.module';
import { FormsModule } from '@angular/forms';
import { SharedModule } from '../../shared/shared.module';


@NgModule({
  imports: [
    CommonModule,
    FrasiHEditRoutingModule,
    FormsModule,
    SharedModule
  ],
  declarations: [
    FrasiHEditComponent
  ]
})
export class FrasiHEditModule { }
