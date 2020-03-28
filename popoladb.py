import requests, time


listino = [
    'Ananas',
    'Banane',
    'Pompelmi',
    'Noci',
    'Arachidi',
    'Nocciole',
    'Mandorle tostate',
    'Melinda',
    'Mele Golden ruggine',
    'Mele imperatore ',
    'Mele verdi ',
    'Mele Star',
    'Mele Fudj',
    'Mele Pink ',
    'Limoni ',
    'Limoni naturali ',
    'Pere Abate ',
    'Pere Kaiser ',
    'Pere Decana',
    'Funghi',
    'Funghi misti',
    'Fragole ',
    'Arance grosse',
    'Arance piccole',
    'Arance naturali ',
    'Mandarini ',
    'Clementini',
    'Pomodori marinda ',
    'Piccadilli ',
    'Naomi ',
    'Datterino ',
    'Asparagi ',
    'Pomodori ovetto',
    'Pomodoro grappolo ',
    'Pomodoro s. Marzano rosso ',
    'Zucchine ',
    'Cetrioli ',
    'Cetrioli lunghi',
    'Melanzane lunghe ',
    'Melanzane tonde',
    'Melanzane viola ',
    'Peperone giallo',
    'Peperone rosso ',
    'Finocchio ',
    'Finocchio piccolo ',
    'Lischeri ',
    'Erbe miste ',
    'Cicoria ',
    'Cicoria punte ',
    'Cime di rapa ',
    'Bietola ',
    'Spinaci ',
    'Cavolo cappuccio bianco ',
    'Cavolo cappuccio viola',
    'Verza',
    'Cavolo fiore ',
    'Cavolo romano ',
    'Carciofi ',
    'Carciofini ',
    'Patate ',
    'Aglio',
    'Cipolla bianca ',
    'Cipolla rossa ',
    'Cipolla dorate ',
    'Cipolla piatta',
    'Cipolla fresca ',
    'Cipolla tropea ',
    'Insalata tonda ',
    'Insalata lunga ',
    'Insalata gentilina ',
    'Insalata canasta',
    'Insalata brasiliana',
    'Insalata riccia ',
    'Insalata scarola ',
    'Insalata mista',
    'Radicchio tondo',
    'Radicchio lungo ',
    'Radicchio originale ',
    'Radicchio variegato ',
    'Cicorino',
    'Rucola ',
    'Ravanelli ',
    'Valeriana',
    'Rape ',
    'Carote ',
    'Carote mazzi ',
    'Sedano ',
    'Prezzemolo ',
    'Zacca gialla ',
    'Fagioli gialli',
    'Fagioli borlotti ',
    'Fagioli cannellini ',
    'Ceci ',
    'Legumi misti'
]


def insert(nome):
    url = "http://95.110.224.168:1001/prodotti"

    payload = "{\"tipo\": \""+nome+"\"}"
    headers = {
        'content-type': "application/json",
        'accept': "application/json"
        }

    response = requests.request("POST", url, data=payload, headers=headers)

    # print(response.text)

for x in listino:
    insert(x)
    time.sleep(1)