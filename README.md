# Projekt: System wspomagający organizację konferencji
## Specyfikacja autorstwa [Piotra Wieczorka](http://www.ii.uni.wroc.pl/~piotrek/)

Jesteś odpowiedzialny za implementację systemu wspomagającego organizację i przeprowadzenie konferencji. Twoim zadaniem jest zaimplementowanie zdefiniowanego poniżej API.

System ma udostępniać API niezależnym aplikacjom działającym na urządzeniach mobilnych uczestników konferencji. Niemniej jednak, ze względu na to, że interesuje nas tematyka baz danych przedmiotem projektu jest stworzenie wersji deweloperskiej systemu, w której wywołania API będą wczytywane z dostarczonego pliku.

Opis problemu

Konferencja obejmuje od kilku do kilkunastu wydarzeń, z których każde zawiera do kilkudziesięciu referatów. Każde wydarzenie posiada datę rozpoczęcia i zakończenia, wydarzenia mogą się pokrywać.

Uczestnicy (kilkaset osób) rejestrują się na dowolnie wybrane przez siebie wydarzenia. Każdy uczestnik może wygłaszać dowolną liczbę referatów (być może 0). Ponadto uczestnicy mogą nawiązywać (symetryczne) znajomości z innymi uczestnikami.

Uczestnicy korzystają z systemu poprzez aplikację na urządzeniu mobilnym, która umożliwia przeglądanie danych konferencji, planu wydarzeń dla danego uczestnika, a także proponowanie i dowiadywanie się o dodatkowych referatach organizowanych spontanicznie – uczestnik proponuje referat, organizator konferencji go zatwierdza, przydziela salę oraz upublicznia. Aplikacja rejestruje obecność uczestnika na poszczególnych referatach, uczestnik może ocenić każdy z referatów (niezależnie od tego czy referat się już odbył). Każdy uczestnik posiada unikalny login i hasło.

Organizator konferencji może definiować wydarzenia i ich zawartość, przeglądać wszystkie zbierane dane, w tym również rozmaite statystyki dotyczące aktywności uczestników.


(*) open <baza> <login> <password>
// przekazuje dane umożliwiające podłączenie Twojego programu do bazy - nazwę bazy, login oraz hasło, wywoływane dokładnie jeden raz, w pierwszej linii wejścia
// zwraca status OK/ERROR w zależności od tego czy udało się nawiązać połączenie z bazą 

(*) organizer <secret> <newlogin> <newpassword> 
// tworzy uczestnika <newlogin> z uprawnieniami organizatora i hasłem <newpassword>, argument <secret> musi być równy d8578edf8458ce06fbc5bb76a58c5ca4 // zwraca status OK/ERROR 


(*O) event <login> <password> <eventname> <start_timestamp> <end_timestamp> // rejestracja wydarzenia, napis <eventname> jest unikalny

(*O) user <login> <password> <newlogin> <newpassword> // rejestracja nowego uczestnika <login> i <password> służą do autoryzacji wywołującego funkcję, który musi posiadać uprawnienia organizatora, <newlogin> <newpassword> są danymi nowego uczestnika, <newlogin> jest unikalny

(*O) talk <login> <password> <speakerlogin> <talk> <title> <start_timestamp> <room> <initial_evaluation> <eventname> // rejestracja referatu/zatwierdzenie referatu spontanicznego, <talk> jest unikalnym identyfikatorem referatu, <initial_evaluation> jest oceną organizatora w skali 0-10 – jest to ocena traktowana tak samo jak ocena uczestnika obecnego na referacie, <eventname> jest nazwą wydarzenia, którego częścią jest dany referat - może być pustym napisem, co oznacza, że referat nie jest przydzielony do jakiegokolwiek wydarzenia

(*U) register_user_for_event <login> <password> <eventname> // rejestracja uczestnika <login> na wydarzenie <eventname>

(*U) attendance <login> <password> <talk> // odnotowanie faktycznej obecności uczestnika <login> na referacie <talk>

(*U) evaluation <login> <password> <talk> <rating> // ocena referatu <talk> w skali 0-10 przez uczestnika <login>

(O) reject <login> <password> <talk> // usuwa referat spontaniczny <talk> z listy zaproponowanych,

(U) proposal  <login> <password> <talk> <title> <start_timestamp> // propozycja referatu spontanicznego, <talk> - unikalny identyfikator referatu

(U) friends <login1> <password> <login2> // uczestnik <login1> chce nawiązać znajomość z uczestnikiem <login2>, znajomość uznajemy za nawiązaną jeśli obaj uczestnicy chcą ją nawiązać tj. po wywołaniach friends <login1> <password1> <login2> i friends <login2> <password2> <login1>

(*N) user_plan <login> <limit> // zwraca plan najbliższych referatów z wydarzeń, na które dany uczestnik jest zapisany (wg rejestracji na wydarzenia) posortowany wg czasu rozpoczęcia, wypisuje pierwsze <limit> referatów, przy czym 0 oznacza, że należy wypisać wszystkie
// Atrybuty zwracanych krotek: 
   <login> <talk> <start_timestamp> <title> <room>

(*N) day_plan <timestamp> // zwraca listę wszystkich referatów zaplanowanych na dany dzień posortowaną rosnąco wg sal, w drugiej kolejności wg czasu rozpoczęcia
//  <talk> <start_timestamp> <title> <room>

(*N) best_talks <start_timestamp> <end_timestamp> <limit> <all> // zwraca referaty rozpoczynające się w  danym przedziale czasowym posortowane malejąco wg średniej oceny uczestników, przy czym jeśli <all> jest równe 1 należy wziąć pod uwagę wszystkie oceny, w przeciwnym przypadku tylko oceny uczestników, którzy byli na referacie obecni, wypisuje pierwsze <limit> referatów, przy czym 0 oznacza, że należy wypisać wszystkie
//  <talk> <start_timestamp> <title> <room>

(*N) most_popular_talks <start_timestamp> <end_timestamp> <limit> // zwraca referaty rozpoczynające się w podanym przedziału czasowego posortowane malejąco wg obecności, wypisuje pierwsze <limit> referatów, przy czym 0 oznacza, że należy wypisać wszystkie
//  <talk> <start_timestamp> <title> <room>

(*U) attended_talks <login> <password> // zwraca dla danego uczestnika referaty, na których był obecny 
//  <talk> <start_timestamp> <title> <room>

(*O) abandoned_talks <login> <password>  <limit> // zwraca listę referatów posortowaną malejąco wg liczby uczestników <number> zarejestrowanych na wydarzenie obejmujące referat, którzy nie byli na tym referacie obecni, wypisuje pierwsze <limit> referatów, przy czym 0 oznacza, że należy wypisać wszystkie
//  <talk> <start_timestamp> <title> <room> <number>

(N) recently_added_talks <limit> // zwraca listę ostatnio zarejestrowanych referatów, wypisuje ostatnie <limit> referatów wg daty zarejestrowania, przy czym 0 oznacza, że należy wypisać wszystkie
//  <talk> <speakerlogin> <start_timestamp> <title> <room>

(U/O) rejected_talks <login> <password> // jeśli wywołujący ma uprawnienia organizatora zwraca listę wszystkich odrzuconych referatów spontanicznych, w przeciwnym przypadku listę odrzuconych referatów wywołującego ją uczestnika 
//  <talk> <speakerlogin> <start_timestamp> <title>

(O) proposals <login> <password> // zwraca listę propozycji referatów spontanicznych do zatwierdzenia lub odrzucenia, zatwierdzenie lub odrzucenie referatu polega na wywołaniu przez organizatora funkcji talk lub reject z odpowiednimi parametrami
//  <talk> <speakerlogin> <start_timestamp> <title>

(U) friends_talks <login> <password> <start_timestamp> <end_timestamp> <limit> // lista referatów  rozpoczynających się w podanym przedziale czasowym wygłaszanych przez znajomych danego uczestnika posortowana wg czasu rozpoczęcia, wypisuje pierwsze <limit> referatów, przy czym 0 oznacza, że należy wypisać wszystkie
//  <talk> <speakerlogin> <start_timestamp> <title> <room>

(U) friends_events <login> <password> <event> // lista znajomych uczestniczących w danym wydarzeniu
//  <login> <event> <friendlogin> 

(U) recommended_talks <login> <password> <start_timestamp> <end_timestamp> <limit> // zwraca referaty rozpoczynające się w podanym przedziale czasowym, które mogą zainteresować danego uczestnika (zaproponuj parametr <score> obliczany na podstawie dostępnych danych – ocen, obecności, znajomości itp.), wypisuje pierwsze <limit> referatów wg nalepszego <score>, przy czym 0 oznacza, że należy wypisać wszystkie
//  <talk> <speakerlogin> <start_timestamp> <title> <room> <score>