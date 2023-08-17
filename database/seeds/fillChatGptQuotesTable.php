<?php

use Illuminate\Database\Seeder;

class fillChatGptQuotesTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        $quotes = [
            "When the game server restarts, take up interpretive dance as your new in-game emote!",
            "Game server restarting? Time to perfect your air guitar skills!",
            "Turn the server restart into a dramatic soap opera episode with your character as the lead!",
            "Why not use the downtime to practice your ninja moves in real life? Server restarts are just opportunities in disguise!",
            "Game server taking a break? Guess it's your turn to finally socialize... with actual people!",
            "During server restarts, start a 'Guess the Patch Notes' contest and watch the wild theories flow!",
            "Server restarts are just nature's way of telling you to go outside and pet some real-life animals.",
            "Collect and trade imaginary server restart trading cards with fellow gamers!",
            "Server restarting? It's your cue to perfect your snack-fetching speedrun!",
            "Time to show off your interpretive reading skills with an epic novel during the server downtime!",
            "Practice your motivational speech skills—someday your character might need one in-game!",
            "Hear that? It's the sound of a thousand gamers collectively realizing they forgot to shower.",
            "Server restarts are like surprise parties—you just never know when they're going to pop up!",
            "Who needs virtual adventures when you can clean your actual, real-life dungeon during server restarts?",
            "Server restarts: the perfect excuse to brush up on your high school algebra. No, really!",
            "Server restarts: when gamers become amateur stand-up comedians in global chat!",
            "During downtime, challenge your friends to a 'Who Can Stack More Random Objects' competition.",
            "Game server taking a nap? Time to brush up on your quantum physics knowledge... or just take a nap too.",
            "Turn server restarts into 'Bring Your Pet to the Keyboard' events!",
            "Server restarts: your chance to practice your speed typing while chatting with fellow restless players!",
            "Grab your accordion and start a virtual band during the server's impromptu concert break!",
            "Game server rebooting? Perfect time to start a virtual food fight in the main square!",
            "Server restarts are just life's way of telling you to finally organize your inventory.",
            "Craft your own in-game mini-games out of cardboard during server restarts!",
            "Use the downtime to master the art of balancing things on your character's head.",
            "Server restarts: because even digital worlds need coffee breaks!",
            "Create a pop-up trivia show in global chat and see who's the true master of useless knowledge!",
            "Knit your character a cozy new armor set during the server's fashion makeover.",
            "During server restarts, offer your best dramatic readings of error messages in a Shakespearean accent.",
            "Turn server downtimes into 'Impersonate Your Favorite NPC' contests!",
            "Server restarts are like life's way of saying, 'Have you called your mom lately?'",
            "Channel your inner philosopher and contemplate the meaning of life, avatars, and loot.",
            "Server restarts: the perfect time to discover your hidden talent for virtual underwater basket weaving!",
            "Take up gardening in-game during server restarts. Your pixelated flowers will thank you!",
            "Turn server restarts into an opportunity to showcase your world-class chair spinning skills.",
            "During downtime, organize a scavenger hunt in global chat and lead your fellow gamers on a wild goose chase.",
            "Server restarts: the ultimate justification for attempting that elusive triple backflip in your living room.",
            "Craft in-game 'Be Right Back' signs for your character to hold during server restarts!",
            "Seize the moment to finally write that in-game romance novel you've been dreaming of.",
            "Use server restarts to conduct important scientific experiments, like determining the optimal way to stack pancakes.",
            "Game server down? Perfect time to start your very own interpretive puppet show!",
            "Practice your motivational speeches for your guild mates while the server takes a breather.",
            "During downtime, compile a list of the most obscure and unlikely server restart excuses.",
            "Server restarts: your chance to become a virtual food critic and review all in-game cuisine.",
            "Host a 'Best Dressed Character' fashion show during the server's impromptu catwalk session!",
            "Challenge fellow gamers to a 'Guess the Developer's Favorite Color' contest during restarts.",
            "During server downtime, host a speed-building competition and see who can construct the tallest tower of virtual Jenga blocks!",
            "Server restarts are like the universe's way of saying, 'Maybe it's time for a dance party...'",
            "Get your virtual character into shape with in-game yoga sessions during server restarts!",
            "Server restarts: the perfect excuse to finally create that interpretive sand art masterpiece!",
            "Server restarting? Time to perfect your air guitar riffs!",
            "Embrace your inner mime: act out your character's adventures while the server takes a nap.",
            "Why not spend the downtime teaching your pet goldfish some new tricks?",
            "During server restarts, develop your own in-game interpretive dance emote!",
            "Game server taking a break? Practice your 'infinite loop' dance moves!",
            "Plan a virtual tea party with your fellow adventurers and gossip about the latest patch notes.",
            "Time to start an in-game book club and discuss the complexities of 'The Art of Server Restart.'",
            "During the restart, challenge your friends to a virtual staring contest.",
            "Craft virtual snow angels in-game while the server enjoys a cold reboot.",
            "Use the downtime to finally calculate how many pixels there are in a server restart!",
            "Server restarts: the universe's way of saying 'It's time to brush up on your interpretive knitting!'",
            "Turn the server restart into a worldwide flash mob dance party!",
            "Game server snoozing? Perfect opportunity to teach your character to moonwalk.",
            "During server downtime, practice your role-playing by narrating your breakfast routine.",
            "Why not use the break to become a virtual cloud-watching champion?",
            "Create a 'Server Restart Impersonation' contest and see who can mimic it best!",
            "Server restarts: the ideal time to showcase your extensive collection of in-game rubber duckies.",
            "Organize a 'Find the Invisible Treasure' event and watch as players search for nothing!",
            "Take up interpretive baking: make virtual cookies while the server recharges.",
            "Server restarts: a chance for your character to star in their very own one-person Broadway show!",
            "Host a virtual talent show during the downtime and be amazed by the hidden skills of your fellow gamers.",
            "Game server napping? It's your cue to practice your interpretive pogo-stick routine!",
            "Turn server restarts into 'Guess the Loading Screen Quote' contests!",
            "Channel your inner philosopher and ponder the mysteries of respawn mechanics.",
            "Use server restarts to finally teach your character to perform Shakespearean soliloquies.",
            "During the downtime, offer in-game life advice to confused NPCs. They'll appreciate it.",
            "Why not write and perform an in-game stand-up comedy routine while the server grabs a coffee?",
            "Server restarts: your chance to become a world-renowned in-game botanist.",
            "Practice your virtual sandcastle-building skills during the server's beach-themed restart.",
            "Use the break to create an in-game museum showcasing all your characters' quirky outfits.",
            "Server downtime: when gamers discover their true calling as virtual synchronized swimmers.",
            "Challenge your guild to a 'Most Dramatic Server Restart Reenactment' competition!",
            "Game server taking a siesta? It's time to organize a virtual scavenger hunt!",
            "During server restarts, offer virtual in-game cooking lessons. Burnt pixels are the best kind!",
            "Turn downtime into a virtual reality within a virtual reality. It's like server-ception!",
            "Server restarts: when gamers find out they have a talent for reciting the server error codes from memory.",
            "During the break, stage an epic dance battle in global chat: Emote Wars!",
            "Why not use the restart to meditate and achieve pixelated enlightenment?",
            "Server restarts: your cue to become the world's greatest in-game interpretive meteorologist!",
            "Craft virtual snowmen during server restarts and build an entire snowman village!",
            "Turn the downtime into a 'Design Your Dream In-Game Mount' art contest!",
            "Use server restarts to master the art of virtual origami. Paper cuts? Not an issue!",
            "Game server napping? It's the perfect time to practice your in-game interpretive shadow puppetry!",
            "During the restart, start a worldwide virtual snowball fight!",
            "Server restarts: your opportunity to become a pixelated sudoku champion!",
            "Host a 'Name That Bug' challenge and see who can come up with the most creative server glitch names.",
            "Why not choreograph an in-game musical extravaganza for your character during the restart?",
            "Server restarts: the universe's way of saying 'Unleash your inner pixel artist!'",
            "During the downtime, host a 'Best Dressed Server Restart' fashion show and parade your pixelated couture.",
            "Turn server restarts into a 'Guess the Sound Effect' game and let your imagination run wild!",
        ];

        foreach ($quotes as $id => $quote) {
            \App\ChatGptQuotes::create(
                [
                    'quotes' => $quote
                ]
            );
        }
    }
}
