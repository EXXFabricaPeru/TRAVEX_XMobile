import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { AmountCurrencyPipe } from './amount-currency.pipe';


@NgModule({
  declarations: [AmountCurrencyPipe],
  imports: [CommonModule],
  exports: [AmountCurrencyPipe]
})
export class PipesModule { }
