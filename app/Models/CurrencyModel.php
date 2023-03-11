<?php namespace App\Models;

use CodeIgniter\Model;

class CurrencyModel extends BaseModel
{
    protected $builder;
    protected $builderPaymentSettings;

    public function __construct()
    {
        parent::__construct();
        $this->builder = $this->db->table('currencies');
        $this->builderPaymentSettings = $this->db->table('payment_settings');
    }

    //set selected currency
    public function setSelectedCurrency()
    {
        $currencyCode = inputPost('currency');
        $currency = $this->getCurrencyByCode($currencyCode);
        if (!empty($currency)) {
            $this->session->set('mds_selected_currency', $currency->code);
        }
    }

    //get selected currency
    public function getSelectedCurrency($currencies)
    {
        if ($this->paymentSettings->currency_converter == 1 && !empty($this->session->get('mds_selected_currency'))) {
            if (isset($currencies[$this->session->get('mds_selected_currency')])) {
                return $currencies[$this->session->get('mds_selected_currency')];
            }
        }
        return $this->getDefaultCurrency($currencies);
    }

    //get default currency
    public function getDefaultCurrency($currencies)
    {
        if (isset($currencies[$this->paymentSettings->default_currency])) {
            return $currencies[$this->paymentSettings->default_currency];
        }
        $currencies = $this->getCurrencies();
        return @$currencies[0];
    }

    //add currency
    public function addCurrency()
    {
        $data = [
            'code' => inputPost('code'),
            'name' => inputPost('name'),
            'symbol' => inputPost('symbol'),
            'currency_format' => inputPost('currency_format'),
            'symbol_direction' => inputPost('symbol_direction'),
            'space_money_symbol' => inputPost('space_money_symbol'),
            'status' => inputPost('status')
        ];
        return $this->builder->insert($data);
    }

    //update currency
    public function editCurrency($id)
    {
        $data = [
            'code' => inputPost('code'),
            'name' => inputPost('name'),
            'symbol' => inputPost('symbol'),
            'currency_format' => inputPost('currency_format'),
            'symbol_direction' => inputPost('symbol_direction'),
            'space_money_symbol' => inputPost('space_money_symbol'),
            'status' => inputPost('status')
        ];
        return $this->builder->where('id', clrNum($id))->update($data);
    }

    //get currencies array
    public function getCurrenciesArray()
    {
        $currencies = $this->getCurrencies();
        $array = array();
        if (!empty($currencies)) {
            foreach ($currencies as $currency) {
                $array[$currency->code] = $currency;
            }
        }
        return $array;
    }

    //get currencies
    public function getCurrencies()
    {
        return $this->builder->orderBy('status DESC, id')->get()->getResult();
    }

    //get currency
    public function getCurrency($id)
    {
        return $this->builder->where('id', clrNum($id))->get()->getRow();
    }

    //get currency by code
    public function getCurrencyByCode($code)
    {
        return $this->builder->where('code', cleanStr($code))->get()->getRow();
    }

    //update currency settings
    public function updateCurrencySettings()
    {
        $data = [
            'default_currency' => inputPost('default_currency'),
            'allow_all_currencies_for_classied' => inputPost('allow_all_currencies_for_classied')
        ];
        $this->builderPaymentSettings->where('id', 1)->update($data);
        $this->updateCurrencyRates($data['default_currency'], null);
        return true;
    }

    //update currency converter settings
    public function updateCurrencyConverterSettings()
    {
        $data = [
            'currency_converter' => inputPost('currency_converter'),
            'auto_update_exchange_rates' => inputPost('auto_update_exchange_rates'),
            'currency_converter_api' => inputPost('currency_converter_api'),
            'currency_converter_api_key' => inputPost('currency_converter_api_key')
        ];
        $this->builderPaymentSettings->where('id', 1)->update($data);
        $this->updateCurrencyRates(null, $data['currency_converter_api'], $data['currency_converter_api_key']);
        return true;
    }

    //update currency rates
    public function updateCurrencyRates($base = null, $service = null, $serviceKey = null)
    {
        if (empty($base)) {
            $base = $this->paymentSettings->default_currency;
        }
        if (empty($service)) {
            $service = $this->paymentSettings->currency_converter_api;
        }
        if (empty($serviceKey)) {
            $serviceKey = $this->paymentSettings->currency_converter_api_key;
        }
        if ($this->paymentSettings->currency_converter == 1) {
            //update exhange rates
            $arrayExchangeRates = array();
            if ($service == 'fixer' && !empty($serviceKey)) {
                $arrayExchangeRates = $this->fixerIoExchangeRates($base, $serviceKey);
            } elseif ($service == 'currencyapi' && !empty($serviceKey)) {
                $arrayExchangeRates = $this->currencyApiNetExchangeRates($base, $serviceKey);
            } elseif ($service == 'openexchangerates' && !empty($serviceKey)) {
                $arrayExchangeRates = $this->openExchangeRatesExchangeRates($base, $serviceKey);
            }
            if (!empty($arrayExchangeRates)) {
                foreach ($arrayExchangeRates as $arrayExchangeRate) {
                    if (isset($arrayExchangeRate['currency']) && isset($arrayExchangeRate['rate'])) {
                        $this->builder->where('code', cleanStr($arrayExchangeRate['currency']))->update(['exchange_rate' => $arrayExchangeRate['rate']]);
                    }
                }
            }
        }
        return true;
    }

    //update currency rate
    public function updateCurrencyRate()
    {
        $currencyId = inputPost('currency_id');
        $exchangeRate = inputPost('exchange_rate');
        $currency = $this->getCurrency($currencyId);
        if (!empty($currency)) {
            $this->builder->where('id', $currency->id)->update(['exchange_rate' => $exchangeRate]);
        }
    }

    // fixer.io Currency Converter
    private function fixerIoExchangeRates($base, $serviceKey)
    {
        $ch = curl_init('http://data.fixer.io/api/latest?access_key=' . $serviceKey . '');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return $this->createRatesArray($response, $base);
    }

    // currencyapi.net Currency Converter
    private function currencyApiNetExchangeRates($base, $serviceKey)
    {
        $ch = curl_init('https://currencyapi.net/api/v1/rates?key=' . $serviceKey . '&base=USD');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return $this->createRatesArray($response, $base);
    }

    // openexchangerates.org Currency Converter
    private function openExchangeRatesExchangeRates($base, $serviceKey)
    {
        $ch = curl_init('https://openexchangerates.org/api/latest.json?app_id=' . $serviceKey . '&base=USD');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return $this->createRatesArray($response, $base);
    }

    //openexchangerates.org Currency Converter
    private function createRatesArray($response, $base)
    {
        $arrayRates = array();
        if (!empty($response)) {
            $responseObject = json_decode($response);
            if (!empty($responseObject) && isset($responseObject->rates)) {
                $rates = $responseObject->rates;
                if (isset($rates->$base)) {
                    $baseRate = $rates->$base;
                    foreach ($rates as $key => $value) {
                        $calculatedRate = 1;
                        if (!empty($baseRate)) {
                            $rate = $value / $baseRate;
                            if (empty($rate)) {
                                $rate = 1;
                            }
                            if (!empty($rate)) {
                                $calculatedRate = number_format($rate, 8, '.', '');
                            }
                        }
                        $item = [
                            'currency' => $key,
                            'rate' => $calculatedRate
                        ];
                        array_push($arrayRates, $item);
                    }
                }
            }
        }
        return $arrayRates;
    }

    //delete currency
    public function deleteCurrency($id)
    {
        $currency = $this->getCurrency($id);
        if (!empty($currency)) {
            return $this->builder->where('id', $currency->id)->delete();
        }
        return false;
    }
}
